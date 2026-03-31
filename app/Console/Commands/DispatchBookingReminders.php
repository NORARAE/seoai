<?php

namespace App\Console\Commands;

use App\Jobs\SendBookingReminderJob;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DispatchBookingReminders extends Command
{
    protected $signature = 'bookings:dispatch-reminders
                            {--dry-run : List bookings that would receive reminders without sending}';

    protected $description = 'Dispatch SMS reminders for upcoming confirmed bookings (run every 15 min via scheduler)';

    public function handle(): int
    {
        $now = Carbon::now('America/Los_Angeles');
        $dryRun = $this->option('dry-run');
        $queued = 0;

        // ── 24-hour reminders ─────────────────────────────────────────────────
        // Dispatched in the 15-minute window starting at 09:00 PT for bookings
        // the following day that haven't received a 24h reminder yet.
        $tomorrowPt = $now->copy()->addDay()->toDateString();
        $window24Start = $now->copy()->setTime(9, 0, 0);
        $window24End = $now->copy()->setTime(9, 15, 0);

        if ($now->between($window24Start, $window24End)) {
            $bookings24h = Booking::query()
                ->whereDate('preferred_date', $tomorrowPt)
                ->whereIn('status', ['confirmed', 'pending'])
                ->whereNull('reminder_24h_sent_at')
                ->where('sms_opted_out', false)
                ->whereNotNull('phone')
                ->get();

            foreach ($bookings24h as $booking) {
                if ($dryRun) {
                    $this->line("  [dry-run 24h] Booking #{$booking->id} — {$booking->name} — {$tomorrowPt}");
                } else {
                    SendBookingReminderJob::dispatch($booking->id, '24h')->onQueue('default');
                    $queued++;
                }
            }
        }

        // ── 2-hour reminders ──────────────────────────────────────────────────
        // Target: bookings today whose preferred_time is 115-130 minutes away.
        $todayPt = $now->toDateString();
        $time2hStart = $now->copy()->addMinutes(115)->format('H:i:s');
        $time2hEnd = $now->copy()->addMinutes(130)->format('H:i:s');

        $bookings2h = Booking::query()
            ->whereDate('preferred_date', $todayPt)
            ->whereBetween('preferred_time', [$time2hStart, $time2hEnd])
            ->whereIn('status', ['confirmed', 'pending'])
            ->whereNull('reminder_2h_sent_at')
            ->where('sms_opted_out', false)
            ->whereNotNull('phone')
            ->get();

        foreach ($bookings2h as $booking) {
            if ($dryRun) {
                $this->line("  [dry-run 2h] Booking #{$booking->id} — {$booking->name} — {$booking->preferred_time}");
            } else {
                SendBookingReminderJob::dispatch($booking->id, '2h')->onQueue('default');
                $queued++;
            }
        }

        if (!$dryRun && $queued > 0) {
            $this->info("Queued {$queued} booking reminder(s).");
        }

        return self::SUCCESS;
    }
}
