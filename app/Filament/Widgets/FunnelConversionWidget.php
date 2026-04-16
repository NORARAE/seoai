<?php

namespace App\Filament\Widgets;

use App\Models\FunnelEvent;
use App\Models\QuickScan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FunnelConversionWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getHeading(): ?string
    {
        return 'Funnel Conversion Metrics';
    }

    protected function getDescription(): ?string
    {
        return 'Full pipeline: Homepage → Scan → Upgrade → Deployment';
    }

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        if (!Auth::user()?->canApproveUsers()) {
            return [];
        }

        $counts = FunnelEvent::query()
            ->selectRaw('event_name, count(*) as count')
            ->groupBy('event_name')
            ->pluck('count', 'event_name');

        $get = fn(string $event): int => (int) ($counts[$event] ?? 0);

        $pct = function (int $num, int $den): string {
            return $den > 0 ? round($num / $den * 100, 1) . '%' : '—';
        };

        $color = function (int $num, int $den): string {
            if ($den === 0) return 'gray';
            $rate = $num / $den * 100;
            if ($rate >= 50) return 'success';
            if ($rate >= 20) return 'warning';
            return 'danger';
        };

        // ── Core funnel counts ─────────────────────────────────────────
        $homepageClicks = $get(FunnelEvent::HOMEPAGE_CTA_CLICK);
        $scanStarted = $get(FunnelEvent::SCAN_STARTED);
        $scanCompleted = $get(FunnelEvent::SCAN_COMPLETED);
        $upgradeClicks = $get(FunnelEvent::UPGRADE_CLICK);
        $upgradePurchased = $get(FunnelEvent::UPGRADE_PURCHASED);
        $deploymentClicks = $get(FunnelEvent::DEPLOYMENT_CTA_CLICK);
        $onboardingStarted = $get(FunnelEvent::ONBOARDING_STARTED);
        $onboardingCompleted = $get(FunnelEvent::ONBOARDING_COMPLETED);

        // ── Upgrade breakdown by plan ──────────────────────────────────
        $upgradePlans = FunnelEvent::query()
            ->where('event_name', FunnelEvent::UPGRADE_PURCHASED)
            ->whereNotNull('metadata')
            ->get()
            ->pluck('metadata.plan')
            ->countBy()
            ->toArray();

        $upgradeBreakdown = collect($upgradePlans)
            ->map(fn($count, $plan) => match($plan) {
                'diagnostic' => "$count × \$99",
                'fix-strategy' => "$count × \$249",
                'optimization' => "$count × \$489",
                default => "$count × $plan",
            })
            ->implode(', ') ?: 'none yet';

        // ── User segmentation ──────────────────────────────────────────
        $totalUsers = QuickScan::distinct('email')->count('email');
        $avgScansPerUser = $totalUsers > 0
            ? round(QuickScan::where('status', QuickScan::STATUS_SCANNED)->count() / $totalUsers, 1)
            : 0;

        $highIntentUsers = DB::table('quick_scans')
            ->select('email')
            ->where('status', QuickScan::STATUS_SCANNED)
            ->groupBy('email')
            ->havingRaw('count(*) >= 2 OR max(case when upgrade_status = ? then 1 else 0 end) = 1', ['paid'])
            ->count();

        $returnVisitors = DB::table('quick_scans')
            ->select('email')
            ->where('status', QuickScan::STATUS_SCANNED)
            ->groupBy('email')
            ->havingRaw('count(*) >= 2')
            ->count();

        return [
            // Row 1: Acquisition → Scan
            Stat::make('Homepage CTA Clicks', number_format($homepageClicks))
                ->description('Clicks on scan CTAs from homepage')
                ->color('primary'),

            Stat::make('Scans Started', number_format($scanStarted))
                ->description('Checkout initiated → ' . $pct($scanStarted, $homepageClicks) . ' of homepage clicks')
                ->color($color($scanStarted, $homepageClicks)),

            Stat::make('Scans Completed', number_format($scanCompleted))
                ->description('Results delivered → ' . $pct($scanCompleted, $scanStarted) . ' of started')
                ->color($color($scanCompleted, $scanStarted)),

            Stat::make('Scan → Upgrade %', $pct($upgradePurchased, $scanCompleted))
                ->description($upgradeBreakdown)
                ->color($color($upgradePurchased, $scanCompleted)),

            // Row 2: Conversion → Deployment
            Stat::make('Upgrade Clicks', number_format($upgradeClicks))
                ->description('Upgrade checkout initiated → ' . $pct($upgradeClicks, $scanCompleted) . ' of completed scans')
                ->color('primary'),

            Stat::make('Upgrades Purchased', number_format($upgradePurchased))
                ->description('Paid → ' . $pct($upgradePurchased, $upgradeClicks) . ' of upgrade clicks')
                ->color($color($upgradePurchased, $upgradeClicks)),

            Stat::make('Deployment CTA Clicks', number_format($deploymentClicks))
                ->description('High-ticket interest signals')
                ->color('warning'),

            Stat::make('Upgrade → Deploy %', $pct($onboardingStarted, $upgradePurchased))
                ->description($onboardingStarted . ' started onboarding, ' . $onboardingCompleted . ' completed')
                ->color($color($onboardingStarted, $upgradePurchased)),

            // Row 3: Segmentation
            Stat::make('Total Customers', number_format($totalUsers))
                ->description('Unique emails with scans')
                ->color('gray'),

            Stat::make('Avg Scans/User', (string) $avgScansPerUser)
                ->description('Completed scans per unique email')
                ->color('gray'),

            Stat::make('High-Intent Users', number_format($highIntentUsers))
                ->description('2+ scans or paid upgrade')
                ->color('success'),

            Stat::make('Return Visitors', number_format($returnVisitors))
                ->description('2+ completed scans (same email)')
                ->color('primary'),
        ];
    }
}
