<?php

namespace App\Filament\Widgets;

use App\Models\SeoOpportunity;
use App\Models\UrlInventory;
use App\Support\CurrentScanResolver;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SeoGrowthOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getHeading(): ?string
    {
        $site = CurrentScanResolver::contextForUser(Auth::user())['site'];

        return 'SEO Growth Overview' . ($site ? " for {$site->domain}" : '');
    }

    protected function getDescription(): ?string
    {
        return CurrentScanResolver::contextForUser(Auth::user())['description'];
    }

    protected function getStats(): array
    {
        $context = CurrentScanResolver::contextForUser(Auth::user());
        $site = $context['site'];
        $scanRun = $context['scan_run'];
        $siteId = $site?->id;
        $siteLabel = $site?->domain ?? 'the selected site';

        $pagesDiscovered = UrlInventory::query()
            ->when($scanRun?->id, fn ($query, int $scanRunId) => $query->where('last_seen_scan_run_id', $scanRunId))
            ->when(! $scanRun && $siteId, fn ($query) => $query->whereRaw('1 = 0'))
            ->count();
        $pagesCrawled = (int) ($scanRun?->pages_crawled ?? 0);
        $indexablePages = UrlInventory::query()
            ->when($scanRun?->id, fn ($query, int $scanRunId) => $query->where('last_seen_scan_run_id', $scanRunId))
            ->where('indexability_status', 'indexable')
            ->count();
        $opportunities = SeoOpportunity::query()
            ->when($scanRun?->id, fn ($query, int $scanRunId) => $query->where('scan_run_id', $scanRunId))
            ->when(! $scanRun && $siteId, fn ($query) => $query->whereRaw('1 = 0'))
            ->count();

        return [
            Stat::make('Pages Discovered', $pagesDiscovered)
                ->description("URLs seen in the current completed scan for {$siteLabel}.")
                ->descriptionIcon('heroicon-o-question-mark-circle')
                ->color('primary'),

            Stat::make('Pages Crawled', $pagesCrawled)
                ->description("Pages successfully crawled in the current completed scan for {$siteLabel}.")
                ->descriptionIcon('heroicon-o-question-mark-circle')
                ->color($pagesCrawled > 0 ? 'success' : 'gray'),

            Stat::make('Indexable Pages', $indexablePages)
                ->description("Current-scan pages eligible for search visibility on {$siteLabel}.")
                ->descriptionIcon('heroicon-o-question-mark-circle')
                ->color($indexablePages > 0 ? 'success' : 'gray'),

            Stat::make('SEO Opportunities', $opportunities)
                ->description("Priority opportunities detected in the current completed scan for {$siteLabel}.")
                ->descriptionIcon('heroicon-o-question-mark-circle')
                ->color($opportunities > 0 ? 'warning' : 'gray'),
        ];
    }
}
