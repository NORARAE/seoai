<?php

namespace App\Jobs;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client as TwilioClient;

class SendBookingReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 60;

    public function __construct(
        public readonly int $bookingId,
        public readonly string $reminderType = '24h', // '24h' | '1h'
    ) {}

    public function handle(): void
    {
        $booking = Booking::with('consultType')->find($this->bookingId);

        if (! $booking) {
            Log::channel('booking')->warning('SendBookingReminderJob: booking not found', [
                'booking_id' => $this->bookingId,
            ]);
            return;
        }

        // Guard: skip if cancelled, or no phone, or already reminded, or opted out
        if (
            in_array($booking->status, ['cancelled', 'completed']) ||
            $booking->sms_opted_out ||
            $booking->reminder_sent_at !== null ||
            empty($booking->phone)
        ) {
            return;
        }

        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from  = config('services.twilio.from');

        if (! $sid || ! $token || ! $from) {
            Log::channel('booking')->info('SMS reminders skipped — Twilio not configured', [
                'booking_id' => $this->bookingId,
            ]);
            return;
        }

        $message = $this->buildMessage($booking);

        try {
            $twilio = new TwilioClient($sid, $token);
            $twilio->messages->create($booking->phone, [
                'from' => $from,
                'body' => $message,
            ]);

            $booking->update(['reminder_sent_at' => now()]);

            Log::channel('booking')->info('Booking reminder SMS sent', [
                'booking_id'    => $booking->id,
                'reminder_type' => $this->reminderType,
                'phone'         => substr($booking->phone, 0, 4) . '****',
            ]);
        } catch (\Exception $e) {
            Log::channel('booking')->error('Booking reminder SMS failed', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
            ]);
            // Re-throw to let the queue retry (respects $tries / $backoff)
            throw $e;
        }
    }

    private function buildMessage(Booking $booking): string
    {
        $sessionName = $booking->consultType?->name ?? 'Your consult';
        $date        = $booking->preferred_date->format('l, F j');
        $time        = \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') . ' PT';
        $meet        = $booking->google_meet_link;

        if ($this->reminderType === '1h') {
            $body = "Reminder: {$sessionName} starts in 1 hour — {$time} today.";
        } else {
            $body = "Reminder: {$sessionName} tomorrow, {$date} at {$time}.";
        }

        if ($meet) {
            $body .= " Join: {$meet}";
        }

        $body .= "\nReply STOP to opt out.";

        return $body;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::channel('booking')->error('SendBookingReminderJob permanently failed', [
            'booking_id' => $this->bookingId,
            'error'      => $exception->getMessage(),
        ]);
    }
}
