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
        $now      = Carbon::now('America/Los_Angeles');
        $dryRun   = $this->option('dry-run');
        $queued   = 0;

        // ── 24-hour reminders ─────────────────────────────────────────────────
        // Target: bookings tomorrow (PT) that haven't had a reminder sent yet,
        // dispatched in the 15-minute window starting at 09:00 PT today.
        $tomorrowPt = $now->copy()->addDay()->toDateString();

        $window24Start = $now->copy()->setTime(9, 0, 0);
        $window24End   = $now->copy()->setTime(9, 15, 0);

        if ($now->between($window24Start, $window24End)) {
            $bookings24h = Booking::query()
                ->whereDate('preferred_date', $tomorrowPt)
                ->whereIn('status', ['confirmed', 'pending'])
                ->whereNull('reminder_sent_at')
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

        // ── 1-hour reminders ──────────────────────────────────────────────────
        // Target: bookings today (PT) whose preferred_time is 60-75 minutes from
        // now and haven't had a reminder sent yet.
        $todayPt         = $now->toDateString();
        $targetTimeStart = $now->copy()->addMinutes(60)->format('H:i:s');
        $targetTimeEnd   = $now->copy()->addMinutes(75)->format('H:i:s');

        $bookings1h = Booking::query()
            ->whereDate('preferred_date', $todayPt)
            ->whereBetween('preferred_time', [$targetTimeStart, $targetTimeEnd])
            ->whereIn('status', ['confirmed', 'pending'])
            ->whereNull('reminder_sent_at')
            ->where('sms_opted_out', false)
            ->whereNotNull('phone')
            ->get();

        foreach ($bookings1h as $booking) {
            if ($dryRun) {
                $this->line("  [dry-run 1h] Booking #{$booking->id} — {$booking->name} — {$booking->preferred_time}");
            } else {
                SendBookingReminderJob::dispatch($booking->id, '1h')->onQueue('default');
                $queued++;
            }
        }

        if (! $dryRun && $queued > 0) {
            $this->info("Queued {$queued} booking reminder(s).");
        }

        return self::SUCCESS;
    }
}
