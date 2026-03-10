<?php

namespace App\Filament\Widgets;

use App\Models\InternalLink;
use App\Models\Page;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PageHealthOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Count pages missing titles
        $missingTitles = Page::missingTitle()->count();

        // Count broken pages (status_code >= 400)
        $brokenPages = Page::broken()->count();

        // Count pages awaiting crawl (crawl_status = 'discovered')
        $awaitingCrawl = Page::discovered()->count();

        // Count orphan pages (zero incoming links) - using stored count
        $orphanPages = Page::where('incoming_links_count', 0)->count();

        // Count weak internal link pages (< 2 incoming links, excluding orphans)
        $weakPages = Page::where('incoming_links_count', '>', 0)
            ->where('incoming_links_count', '<', 2)
            ->count();

        return [
            Stat::make('Pages Missing Titles', $missingTitles)
                ->description('Pages without a title tag')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($missingTitles > 0 ? 'danger' : 'success'),

            Stat::make('Broken Pages', $brokenPages)
                ->description('HTTP status code >= 400')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color($brokenPages > 0 ? 'danger' : 'success'),

            Stat::make('Pages Awaiting Crawl', $awaitingCrawl)
                ->description('Discovered but not yet crawled')
                ->descriptionIcon('heroicon-o-clock')
                ->color($awaitingCrawl > 0 ? 'warning' : 'success'),

            Stat::make('Potential Orphan Pages', $orphanPages)
                ->description('Pages with zero incoming links')
                ->descriptionIcon('heroicon-o-link-slash')
                ->color($orphanPages > 0 ? 'warning' : 'success'),

            Stat::make('Weak Internal Links', $weakPages)
                ->description('Pages with fewer than 2 incoming links')
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->color($weakPages > 0 ? 'warning' : 'success'),
        ];
    }
}
