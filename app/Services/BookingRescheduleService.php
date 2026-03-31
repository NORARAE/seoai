<?php

namespace App\Services;

use App\Models\Booking;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmed;

class BookingRescheduleService
{
    /**
     * Number of hours before the booking when reschedules are no longer allowed.
     */
    private const CUTOFF_HOURS = 6;

    /**
     * Retrieve a booking by its public token or fail.
     */
    public function findByToken(string $token): Booking
    {
        return Booking::with('consultType')
            ->where('public_booking_token', $token)
            ->firstOrFail();
    }

    /**
     * Returns whether the booking can still be rescheduled.
     */
    public function canReschedule(Booking $booking): bool
    {
        if (in_array($booking->status, ['cancelled', 'completed'])) {
            return false;
        }

        $appointmentAt = Carbon::parse(
            $booking->preferred_date->format('Y-m-d') . ' ' . $booking->preferred_time,
            'America/Los_Angeles',
        );

        return $appointmentAt->diffInHours(now(), false) < -self::CUTOFF_HOURS;
    }

    /**
     * Reschedule the booking to a new date + time.
     *
     * Returns an error string on failure, null on success.
     */
    public function reschedule(Booking $booking, string $newDate, string $newTime): ?string
    {
        // Slot conflict check (exclude the current booking itself)
        $conflict = Booking::whereDate('preferred_date', $newDate)
            ->where('preferred_time', $newTime)
            ->whereIn('status', ['pending', 'confirmed', 'awaiting_payment'])
            ->where('id', '!=', $booking->id)
            ->exists();

        if ($conflict) {
            return 'That time slot is already taken. Please choose another.';
        }

        $updates = [
            'preferred_date' => $newDate,
            'preferred_time' => $newTime,
            'reschedule_count' => $booking->reschedule_count + 1,
            'last_rescheduled_at' => now(),
            // Reset per-type reminder flags so reminders fire again for the new time
            'reminder_sent_at' => null,
            'reminder_24h_sent_at' => null,
            'reminder_2h_sent_at' => null,
        ];

        // Update Google Calendar event if connected
        if ($booking->google_event_id && config('services.google.calendar_enabled', false)) {
            try {
                $calendarService = app(GoogleCalendarService::class);
                $result = $calendarService->rescheduleBookingEvent(
                    $booking->google_event_id,
                    $booking,
                    $newDate,
                    $newTime,
                );

                if (isset($result['meet_link'])) {
                    $updates['google_meet_link'] = $result['meet_link'];
                }
            } catch (\Exception $e) {
                Log::channel('booking')->error('BookingRescheduleService: Calendar update failed', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
                // Non-fatal — proceed with reschedule anyway
            }
        }

        $booking->update($updates);
        $booking->refresh()->load('consultType');

        // Send updated confirmation email
        try {
            Mail::to($booking->email)->queue(new BookingConfirmed($booking, rescheduled: true));
        } catch (\Exception $e) {
            Log::channel('booking')->error('BookingRescheduleService: email failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }
}
