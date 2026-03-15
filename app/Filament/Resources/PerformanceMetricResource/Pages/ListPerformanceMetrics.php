<?php

namespace App\Filament\Resources\PerformanceMetricResource\Pages;

use App\Filament\Resources\PerformanceMetricResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListPerformanceMetrics extends ListRecords
{
    protected static string $resource = PerformanceMetricResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sync')
                ->label('Sync GSC Data')
                ->icon('heroicon-o-arrow-path')
                ->url(route('filament.admin.resources.sites.index'))
                ->color('primary'),
        ];
    }
}
