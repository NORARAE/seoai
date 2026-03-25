<?php

namespace App\Filament\Resources\SiteCrawlSettingResource\Pages;

use App\Filament\Resources\SiteCrawlSettingResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;

class ListSiteCrawlSettings extends ListRecords
{
    protected static string $resource = SiteCrawlSettingResource::class;

    public function getSubheading(): string|HtmlString|null
    {
        return new HtmlString(
            '<span class="inline-flex items-center gap-x-1.5 rounded-full px-3 py-1 text-xs font-semibold bg-slate-500/10 text-slate-300 ring-1 ring-inset ring-slate-500/25">'
            . '<svg class="size-1.5 fill-slate-400" viewBox="0 0 6 6" aria-hidden="true"><circle cx="3" cy="3" r="3"/></svg>'
            . 'SITE CONFIGURATION'
            . '</span>'
            . '<span class="ml-3 text-sm text-gray-400">Operator-controlled crawl parameters (max pages, crawl delay, depth). Changes take effect on the next scan.</span>'
        );
    }
}
