<?php

namespace App\Filament\Resources\CrawlPolicyResource\Pages;

use App\Filament\Resources\CrawlPolicyResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;

class ListCrawlPolicies extends ListRecords
{
    protected static string $resource = CrawlPolicyResource::class;

    public function getSubheading(): string|HtmlString|null
    {
        return new HtmlString(
            '<span class="inline-flex items-center gap-x-1.5 rounded-full px-3 py-1 text-xs font-semibold bg-slate-500/10 text-slate-300 ring-1 ring-inset ring-slate-500/25">'
            . '<svg class="size-1.5 fill-slate-400" viewBox="0 0 6 6" aria-hidden="true"><circle cx="3" cy="3" r="3"/></svg>'
            . 'SITE CONFIGURATION'
            . '</span>'
            . '<span class="ml-3 text-sm text-gray-400">Robots.txt-derived crawl rules, stored per site. Updated each time a crawl fetches the site\&#39;s robots.txt.</span>'
        );
    }
}
