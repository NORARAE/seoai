<?php

namespace App\Filament\Resources\SeoOpportunityResource\Pages;

use App\Filament\Resources\SeoOpportunityResource;
use App\Filament\Widgets\ScanContextHeaderWidget;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;

class ListSeoOpportunities extends ListRecords
{
    protected static string $resource = SeoOpportunityResource::class;

    public function getSubheading(): string|HtmlString|null
    {
        if ((int) data_get(request()->input('tableFilters', []), 'current_scan.scan_run_id') > 0) {
            return new HtmlString(
                '<span class="inline-flex items-center gap-x-1.5 rounded-full px-3 py-1 text-xs font-semibold bg-sky-500/10 text-sky-300 ring-1 ring-inset ring-sky-500/25">'
                . '<svg class="size-1.5 fill-sky-400" viewBox="0 0 6 6" aria-hidden="true"><circle cx="3" cy="3" r="3"/></svg>'
                . 'SELECTED SNAPSHOT'
                . '</span>'
                . '<span class="ml-3 text-sm text-gray-400">Showing opportunities created from the selected snapshot only. If that snapshot did not produce any recommendations, this page stays empty instead of switching to older results.</span>'
            );
        }

        return new HtmlString(
            '<span class="inline-flex items-center gap-x-1.5 rounded-full px-3 py-1 text-xs font-semibold bg-violet-500/10 text-violet-300 ring-1 ring-inset ring-violet-500/25">'
            . '<svg class="size-1.5 fill-violet-400" viewBox="0 0 6 6" aria-hidden="true"><circle cx="3" cy="3" r="3"/></svg>'
            . 'SITE-WIDE HISTORY'
            . '</span>'
            . '<span class="ml-3 text-sm text-gray-400">Showing the opportunity backlog for the current site across snapshots. Use <strong class="text-gray-300">Status</strong> to focus on what still needs review.</span>'
        );
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ScanContextHeaderWidget::class,
        ];
    }
}
