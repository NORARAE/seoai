<?php

namespace App\Filament\Resources\ScanRunResource\Pages;

use App\Filament\Resources\ScanRunResource;
use App\Filament\Resources\ScanRunResource\Widgets\ScanRunStatsOverview;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\HtmlString;

class ViewScanRun extends ViewRecord
{
    protected static string $resource = ScanRunResource::class;

    public function getSubheading(): string|HtmlString|null
    {
        return new HtmlString(
            '<span class="text-sm text-gray-400">You are viewing one snapshot only. Use this page to inspect what was discovered, queued, and recommended during this scan, not later site changes.</span>'
        );
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ScanRunStatsOverview::make([
                'record' => $this->getRecord(),
            ]),
        ];
    }
}
