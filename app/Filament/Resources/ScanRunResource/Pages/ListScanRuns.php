<?php

namespace App\Filament\Resources\ScanRunResource\Pages;

use App\Filament\Resources\ScanRunResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;

class ListScanRuns extends ListRecords
{
    protected static string $resource = ScanRunResource::class;

    public function getSubheading(): string|HtmlString|null
    {
        return new HtmlString(
            '<span class="text-sm text-gray-400">Each row is a site snapshot. Open a scan to review what was discovered, how far it progressed, and which opportunities were created at that point in time.</span>'
        );
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
