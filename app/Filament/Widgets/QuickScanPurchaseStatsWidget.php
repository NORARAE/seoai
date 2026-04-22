<?php

namespace App\Filament\Widgets;

use App\Models\QuickScan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QuickScanPurchaseStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalPaid = QuickScan::where('paid', true)->count();

        $last24h = QuickScan::where('paid', true)
            ->where('created_at', '>=', now()->subDay())
            ->count();

        $today = QuickScan::where('paid', true)
            ->whereDate('created_at', today())
            ->count();

        $upgrades = QuickScan::where('upgrade_status', 'paid')->count();

        return [
            Stat::make('All-time Purchases', $totalPaid)
                ->description('Total paid quick scans')
                ->color('success'),

            Stat::make('Last 24 Hours', $last24h)
                ->description('New paid scans')
                ->color($last24h > 0 ? 'success' : 'gray'),

            Stat::make('Today', $today)
                ->description('Paid scans today')
                ->color($today > 0 ? 'success' : 'gray'),

            Stat::make('Upgrades', $upgrades)
                ->description('Paid upgrade purchases')
                ->color($upgrades > 0 ? 'warning' : 'gray'),
        ];
    }
}
