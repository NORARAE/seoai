<?php

namespace App\Filament\Resources\CrawlQueueResource\Pages;

use App\Filament\Resources\CrawlQueueResource;
use App\Filament\Widgets\ScanContextHeaderWidget;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;

class ListCrawlQueues extends ListRecords
{
    protected static string $resource = CrawlQueueResource::class;

    public function getSubheading(): string|HtmlString|null
    {
        if ((int) data_get(request()->input('tableFilters', []), 'current_scan.scan_run_id') > 0) {
            return new HtmlString(
                '<span class="inline-flex items-center gap-x-1.5 rounded-full px-3 py-1 text-xs font-semibold bg-sky-500/10 text-sky-300 ring-1 ring-inset ring-sky-500/25">'
                . '<svg class="size-1.5 fill-sky-400" viewBox="0 0 6 6" aria-hidden="true"><circle cx="3" cy="3" r="3"/></svg>'
                . 'SELECTED SNAPSHOT'
                . '</span>'
                . '<span class="ml-3 text-sm text-gray-400">Showing queued, processing, completed, and failed work for the selected scan. This stays locked to that scan instead of switching to older site activity.</span>'
            );
        }

        return new HtmlString(
            '<span class="inline-flex items-center gap-x-1.5 rounded-full px-3 py-1 text-xs font-semibold bg-amber-500/10 text-amber-300 ring-1 ring-inset ring-amber-500/25">'
            . '<svg class="size-1.5 fill-amber-400" viewBox="0 0 6 6" aria-hidden="true"><circle cx="3" cy="3" r="3"/></svg>'
            . 'SITE-WIDE HISTORY'
            . '</span>'
            . '<span class="ml-3 text-sm text-gray-400">Showing scan-progress history for the current site. Use <strong class="text-gray-300">Active only</strong> when you want to focus on work that still needs processing.</span>'
        );
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ScanContextHeaderWidget::class,
        ];
    }
}
