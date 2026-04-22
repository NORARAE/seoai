<?php

namespace App\Filament\Resources\QuickScanResource\Pages;

use App\Filament\Resources\QuickScanResource;
use App\Filament\Widgets\QuickScanPurchaseStatsWidget;
use App\Models\QuickScan;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListQuickScans extends ListRecords
{
    protected static string $resource = QuickScanResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn() => QuickScan::count()),

            'scanned' => Tab::make('Scanned')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'scanned'))
                ->badge(fn() => QuickScan::where('status', 'scanned')->count())
                ->badgeColor('success'),

            'paid' => Tab::make('Paid')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'paid'))
                ->badge(fn() => QuickScan::where('status', 'paid')->count())
                ->badgeColor('info'),

            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(fn() => QuickScan::where('status', 'pending')->count())
                ->badgeColor('gray'),

            'error' => Tab::make('Error')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'error'))
                ->badge(fn() => QuickScan::where('status', 'error')->count())
                ->badgeColor('danger'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            QuickScanPurchaseStatsWidget::class,
        ];
    }
}
