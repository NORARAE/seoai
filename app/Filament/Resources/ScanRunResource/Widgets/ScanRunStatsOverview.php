<?php

namespace App\Filament\Resources\ScanRunResource\Widgets;

use App\Models\ScanRun;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ScanRunStatsOverview extends BaseWidget
{
    public ?ScanRun $record = null;

    protected function getStats(): array
    {
        if (! $this->record) {
            return [];
        }

        return [
            Stat::make('Pages Discovered', number_format($this->record->pages_discovered)),
            Stat::make('Pages Crawled', number_format($this->record->pages_crawled)),
            Stat::make('Pages Failed', number_format($this->record->pages_failed)),
            Stat::make('Opportunities Found', number_format($this->record->opportunities_found)),
        ];
    }
}
