<?php

namespace App\Filament\Widgets;

use App\Models\BlockedIp;
use App\Models\SpamLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SpamIntelligenceWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getHeading(): ?string
    {
        return 'Spam Intelligence — Live Overview';
    }

    public static function canView(): bool
    {
        return Auth::user()?->canApproveUsers() ?? false;
    }

    protected function getStats(): array
    {
        $blockedToday  = SpamLog::where('action', 'block')->whereDate('created_at', today())->count();
        $flaggedToday  = SpamLog::where('action', 'flag')->whereDate('created_at', today())->count();
        $unreviewed    = SpamLog::where('is_reviewed', false)->count();
        $blockedIpCount = BlockedIp::count();

        $tsFailures7d = SpamLog::where(fn ($q) =>
            $q->where('turnstile_valid', false)
              ->orWhere('turnstile_reason', 'turnstile_missing')
        )->where('created_at', '>=', now()->subDays(7))->count();

        $topIp = SpamLog::select('ip_address', DB::raw('count(*) as hits'))
            ->where('action', 'block')
            ->where('created_at', '>=', now()->subDays(7))
            ->whereNotNull('ip_address')
            ->groupBy('ip_address')
            ->orderByDesc('hits')
            ->first();

        $topReason = SpamLog::select('reason', DB::raw('count(*) as hits'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('reason')
            ->orderByDesc('hits')
            ->first();

        $total7d = SpamLog::where('created_at', '>=', now()->subDays(7))->count();

        // 7-day daily trend for the description chart
        $daily7d = SpamLog::selectRaw('DATE(created_at) as day, count(*) as cnt')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('cnt', 'day');

        $trend = collect(range(6, 0))->map(function (int $daysAgo) use ($daily7d): int {
            $day = now()->subDays($daysAgo)->toDateString();
            return (int) ($daily7d[$day] ?? 0);
        })->values()->all();

        return [
            Stat::make('Blocked Today', $blockedToday)
                ->description('Submissions hard-blocked today')
                ->descriptionIcon('heroicon-o-shield-exclamation')
                ->color($blockedToday > 0 ? 'danger' : 'gray'),

            Stat::make('Flagged Today', $flaggedToday)
                ->description('Suspicious but allowed through')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($flaggedToday > 0 ? 'warning' : 'gray'),

            Stat::make('Turnstile Failures (7d)', $tsFailures7d)
                ->description('Invalid or missing Turnstile tokens')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color($tsFailures7d > 5 ? 'danger' : 'gray'),

            Stat::make('Spam Attempts (7d)', $total7d)
                ->description('Blocked + flagged last 7 days')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->chart($trend)
                ->color($total7d > 10 ? 'warning' : 'gray'),

            Stat::make('Unreviewed', $unreviewed)
                ->description('Spam logs not yet reviewed')
                ->descriptionIcon('heroicon-o-clock')
                ->color($unreviewed > 20 ? 'warning' : 'gray'),

            Stat::make('Blocked IPs', $blockedIpCount)
                ->description(
                    $topIp
                        ? "Top attacker: {$topIp->ip_address} ({$topIp->hits} hits)"
                        : 'IPs in persistent blocklist'
                )
                ->descriptionIcon('heroicon-o-no-symbol')
                ->color('gray'),

            Stat::make('Top Spam Reason (7d)', $topReason ? str_replace('_', ' ', $topReason->reason) : '—')
                ->description($topReason ? "{$topReason->hits} occurrences" : 'No spam logged yet')
                ->descriptionIcon('heroicon-o-tag')
                ->color('gray'),
        ];
    }
}
