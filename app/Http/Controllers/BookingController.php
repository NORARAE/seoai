<?php

namespace App\Http\Controllers;

use App\Mail\BookingAlert;
use App\Mail\BookingConfirmed;
use App\Models\Booking;
use App\Models\BookingAvailability;
use App\Models\ConsultType;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function index()
    {
        $types = ConsultType::active()->get();

        return view('public.book', compact('types'));
    }

    /**
     * Return available time slots for a date + consult type (AJAX).
     */
    public function getSlots(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'consult_type_id' => 'required|exists:consult_types,id',
        ]);

        $date = Carbon::parse($request->date);
        $type = ConsultType::findOrFail($request->consult_type_id);

        // Check if this day has availability configured
        $availability = BookingAvailability::active()
            ->where('day_of_week', $date->dayOfWeek)
            ->first();

        if (! $availability) {
            return response()->json(['slots' => [], 'message' => 'No availability on this day.']);
        }

        try {
            $calendarService = app(GoogleCalendarService::class);
            $slots = $calendarService->getAvailableSlots($date, $type->duration_minutes);
        } catch (\Exception $e) {
            Log::channel('booking')->warning('Calendar unavailable, falling back to DB-only slots', [
                'error' => $e->getMessage(),
            ]);
            $slots = $this->getFallbackSlots($date, $type->duration_minutes);
        }

        return response()->json(['slots' => $slots]);
    }

    /**
     * Store a new booking.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'consult_type_id' => 'required|exists:consult_types,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:500',
            'message' => 'nullable|string|max:2000',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|date_format:H:i',
        ]);

        $type = ConsultType::findOrFail($validated['consult_type_id']);
        $date = Carbon::parse($validated['preferred_date']);

        // Race condition guard: check slot still available
        $existing = Booking::where('preferred_date', $date->toDateString())
            ->where('preferred_time', $validated['preferred_time'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($existing) {
            return response()->json([
                'message' => 'That time slot was just taken. Please pick another.',
            ], 422);
        }

        $booking = Booking::create([
            ...$validated,
            'status' => 'pending',
        ]);

        // Attempt Google Calendar integration
        try {
            $calendarService = app(GoogleCalendarService::class);
            $result = $calendarService->createBookingEvent($booking);

            $booking->update([
                'google_event_id' => $result['event_id'],
                'google_meet_link' => $result['meet_link'],
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::channel('booking')->error('Google Calendar event creation failed — booking saved as pending', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            // Booking stays as 'pending' for manual review
        }

        $booking->refresh()->load('consultType');

        // Send confirmation emails
        try {
            Mail::to($booking->email)->send(new BookingConfirmed($booking));
            Mail::to(config('services.booking.owner_email', 'hello@seoaico.com'))
                ->send(new BookingAlert($booking));
        } catch (\Exception $e) {
            Log::channel('booking')->error('Booking email failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'consult_type' => $booking->consultType->name,
                'date' => $booking->preferred_date->format('l, F j, Y'),
                'time' => Carbon::parse($booking->preferred_time)->format('g:i A'),
                'meet_link' => $booking->google_meet_link,
                'duration' => $booking->consultType->duration_minutes,
            ],
        ]);
    }

    /**
     * Booking confirmation page.
     */
    public function confirm(Booking $booking)
    {
        $booking->load('consultType');

        return view('public.booking-confirm', compact('booking'));
    }

    /**
     * Show cancellation form.
     */
    public function cancel(Booking $booking)
    {
        $booking->load('consultType');

        return view('public.booking-cancel', compact('booking'));
    }

    /**
     * Process booking cancellation.
     */
    public function processCancel(Booking $booking): JsonResponse
    {
        if ($booking->isCancelled()) {
            return response()->json(['message' => 'Booking already cancelled.'], 400);
        }

        if ($booking->google_event_id) {
            try {
                $calendarService = app(GoogleCalendarService::class);
                $calendarService->cancelEvent($booking->google_event_id);
            } catch (\Exception $e) {
                Log::channel('booking')->error('Failed to cancel Google event', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Booking cancelled.']);
    }

    /**
     * Fallback when Google Calendar is unavailable — use DB bookings only.
     */
    private function getFallbackSlots(Carbon $date, int $durationMinutes): array
    {
        $availability = BookingAvailability::active()
            ->where('day_of_week', $date->dayOfWeek)
            ->first();

        if (! $availability) {
            return [];
        }

        $start = $date->copy()->setTimeFromTimeString($availability->start_time);
        $end = $date->copy()->setTimeFromTimeString($availability->end_time);

        $booked = Booking::where('preferred_date', $date->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('preferred_time')
            ->map(fn ($t) => Carbon::parse($t)->format('H:i'))
            ->toArray();

        $slots = [];
        $cursor = $start->copy();

        while ($cursor->copy()->addMinutes($durationMinutes)->lte($end)) {
            $time = $cursor->format('H:i');
            if (! in_array($time, $booked)) {
                $slots[] = $time;
            }
            $cursor->addMinutes(30);
        }

        return $slots;
    }
}
