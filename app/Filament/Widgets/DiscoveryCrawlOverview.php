<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CrawlQueueResource;
use App\Filament\Resources\UrlInventoryResource;
use App\Models\CrawlQueue;
use App\Models\SeoOpportunity;
use App\Models\UrlInventory;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DiscoveryCrawlOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $discovered   = UrlInventory::count();
        $crawled      = UrlInventory::where('status', 'completed')->count();
        $indexable    = UrlInventory::where('indexability_status', 'indexable')->count();
        $orphanPages  = UrlInventory::where('is_orphan_page', true)
            ->where('indexability_status', 'indexable')
            ->count();
        $pendingOpps  = SeoOpportunity::where('status', 'pending')->count();
        $queueErrors  = CrawlQueue::where('status', 'failed')->count();

        return [
            Stat::make('Discovered URLs', $discovered)
                ->description('Total URLs in inventory')
                ->descriptionIcon('heroicon-o-globe-alt')
                ->color('primary')
                ->url(UrlInventoryResource::getUrl('index')),

            Stat::make('Crawled URLs', $crawled)
                ->description('Successfully extracted pages')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color($crawled > 0 ? 'success' : 'gray')
                ->url(UrlInventoryResource::getUrl('index', ['tableFilters' => ['status' => ['value' => 'completed']]])),

            Stat::make('Indexable URLs', $indexable)
                ->description('Pages eligible for indexing')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color($indexable > 0 ? 'success' : 'gray')
                ->url(UrlInventoryResource::getUrl('index', ['tableFilters' => ['indexability_status' => ['value' => 'indexable']]])),

            Stat::make('Orphan Pages', $orphanPages)
                ->description('Indexable pages with no internal links')
                ->descriptionIcon('heroicon-o-link-slash')
                ->color($orphanPages > 0 ? 'warning' : 'success')
                ->url(UrlInventoryResource::getUrl('index')),

            Stat::make('Pending Opportunities', $pendingOpps)
                ->description('SEO gaps awaiting review')
                ->descriptionIcon('heroicon-o-light-bulb')
                ->color($pendingOpps > 0 ? 'warning' : 'success'),

            Stat::make('Queue Errors', $queueErrors)
                ->description('Failed crawl attempts')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($queueErrors > 0 ? 'danger' : 'success')
                ->url(CrawlQueueResource::getUrl('index', ['tableFilters' => ['status' => ['value' => 'failed']]])),
        ];
    }
}
