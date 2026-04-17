<?php

use App\Jobs\DailyGscSyncJob;
use App\Jobs\SendInactiveUserNudgeJob;
use App\Jobs\WeeklyOpportunityScanJob;
use App\Jobs\MonthlyContentRefreshJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================================================
// AUTOMATED JOBS SCHEDULE
// ============================================================================
// TODO: Fix job scheduling - temporarily disabled for migration

// // Daily: GSC data sync at 2 AM
// Schedule::job(new DailyGscSyncJob())
//     ->dailyAt('02:00')
//     ->name('daily_gsc_sync')
//     ->withoutOverlapping()
//     ->runInBackground();

// // Weekly: Opportunity detection scan every Sunday at 3 AM
// Schedule::job(new WeeklyOpportunityScanJob())
//     ->weeklyOn(0, '03:00') // 0 = Sunday
//     ->name('weekly_opportunity_scan')
//     ->withoutOverlapping()
//     ->runInBackground();

// // Monthly: Content refresh on the 1st of each month at 4 AM
// Schedule::job(new MonthlyContentRefreshJob())
//     ->monthlyOn(1, '04:00')
//     ->name('monthly_content_refresh')
//     ->withoutOverlapping()
//     ->runInBackground();

// Backup: Legacy artisan command for manual GSC sync
// TODO: Fix - scheduled closures cannot run in background
// Schedule::command('gsc:sync')
//     ->dailyAt('02:00')
//     ->withoutOverlapping()
//     ->runInBackground()
//     ->skip(function () {
//         // Skip if job-based sync exists
//         return class_exists(DailyGscSyncJob::class);
//     });

// Phase 4: discovery crawl orchestration
Schedule::command('crawl:dispatch --limit=50')
    ->everyMinute()
    ->name('crawl_dispatch')
    ->withoutOverlapping()
    ->runInBackground();

// Booking SMS reminders (24h + 1h before session)
Schedule::command('bookings:dispatch-reminders')
    ->everyFifteenMinutes()
    ->name('booking_reminders')
    ->withoutOverlapping()
    ->runInBackground();

// Daily: Nudge inactive users who paid but haven't upgraded (3 days post-scan)
Schedule::job(new SendInactiveUserNudgeJob())
    ->dailyAt('10:00')
    ->name('inactive_user_nudge')
    ->withoutOverlapping();

