<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Services\Sms\TwilioSmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBookingReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 60;

    /**
     * @param  int     $bookingId
     * @param  string  $reminderType  '24h' | '2h' | '1h'
     */
    public function __construct(
        public readonly int $bookingId,
        public readonly string $reminderType = '24h',
    ) {
    }

    public function handle(TwilioSmsService $sms): void
    {
        if (!config('services.twilio.sid') || !config('services.twilio.token') || !config('services.twilio.from')) {
            Log::channel('booking')->info('SMS reminders skipped — Twilio not configured', [
                'booking_id' => $this->bookingId,
            ]);
            return;
        }

        $booking = Booking::with('consultType')->find($this->bookingId);

        if (!$booking) {
            Log::channel('booking')->warning('SendBookingReminderJob: booking not found', [
                'booking_id' => $this->bookingId,
            ]);
            return;
        }

        // Guard: skip cancelled/completed, no phone, opted out
        if (
            in_array($booking->status, ['cancelled', 'completed']) ||
            $booking->sms_opted_out ||
            empty($booking->phone)
        ) {
            return;
        }

        // Per-type idempotency guard
        $sentAtField = $this->sentAtField();
        if ($booking->$sentAtField !== null) {
            return;
        }

        $message = $this->buildMessage($booking);

        try {
            $sms->send($booking->phone, $message);

            $booking->update([
                $sentAtField => now(),
                'reminder_sent_at' => now(), // legacy field kept up-to-date
            ]);

            Log::channel('booking')->info('Booking reminder SMS sent', [
                'booking_id' => $booking->id,
                'reminder_type' => $this->reminderType,
                'phone' => substr($booking->phone, 0, 4) . '****',
            ]);
        } catch (\Exception $e) {
            Log::channel('booking')->error('Booking reminder SMS failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Map reminderType to the corresponding DB column name.
     */
    private function sentAtField(): string
    {
        return match ($this->reminderType) {
            '24h' => 'reminder_24h_sent_at',
            '2h' => 'reminder_2h_sent_at',
            default => 'reminder_2h_sent_at', // '1h' treated same as '2h'
        };
    }

    private function buildMessage(Booking $booking): string
    {
        $sessionName = $booking->consultType?->name ?? 'Your consult';
        $date = $booking->preferred_date->format('l, F j');
        $time = \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') . ' PT';
        $meet = $booking->google_meet_link;

        $body = match ($this->reminderType) {
            '24h' => "Reminder: {$sessionName} is tomorrow, {$date} at {$time}.",
            '2h' => "Reminder: {$sessionName} starts in 2 hours — {$time} today.",
            default => "Reminder: {$sessionName} starts in 1 hour — {$time} today.",
        };

        if ($meet) {
            $body .= " Join: {$meet}";
        }

        $body .= "\nReply STOP to opt out.";

        return $body;
    }

    public function failed(\Throwable $exception): void
    {
        Log::channel('booking')->error('SendBookingReminderJob permanently failed', [
            'booking_id' => $this->bookingId,
            'error' => $exception->getMessage(),
        ]);
    }
}
