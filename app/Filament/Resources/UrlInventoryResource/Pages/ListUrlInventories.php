<?php

namespace App\Filament\Resources\UrlInventoryResource\Pages;

use App\Filament\Resources\UrlInventoryResource;
use App\Filament\Widgets\ScanContextHeaderWidget;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;

class ListUrlInventories extends ListRecords
{
    protected static string $resource = UrlInventoryResource::class;

    public function getSubheading(): string|HtmlString|null
    {
        if ((int) data_get(request()->input('tableFilters', []), 'current_scan.scan_run_id') > 0) {
            return new HtmlString(
                '<span class="inline-flex items-center gap-x-1.5 rounded-full px-3 py-1 text-xs font-semibold bg-sky-500/10 text-sky-300 ring-1 ring-inset ring-sky-500/25">'
                . '<svg class="size-1.5 fill-sky-400" viewBox="0 0 6 6" aria-hidden="true"><circle cx="3" cy="3" r="3"/></svg>'
                . 'SELECTED SNAPSHOT'
                . '</span>'
                . '<span class="ml-3 text-sm text-gray-400">Showing pages captured in the selected snapshot only. If this snapshot has no pages yet, the table stays empty instead of switching to older site history.</span>'
            );
        }

        return new HtmlString(
            '<span class="inline-flex items-center gap-x-1.5 rounded-full px-3 py-1 text-xs font-semibold bg-blue-500/10 text-blue-300 ring-1 ring-inset ring-blue-500/25">'
            . '<svg class="size-1.5 fill-blue-400" viewBox="0 0 6 6" aria-hidden="true"><circle cx="3" cy="3" r="3"/></svg>'
            . 'SITE-WIDE HISTORY'
            . '</span>'
            . '<span class="ml-3 text-sm text-gray-400">Showing all discovered pages for the current site across snapshots. Use Scan History when you need to inspect one specific snapshot.</span>'
        );
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ScanContextHeaderWidget::class,
        ];
    }
}
