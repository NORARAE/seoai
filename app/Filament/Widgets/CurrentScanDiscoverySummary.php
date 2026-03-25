<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SeoOpportunityResource;
use App\Filament\Resources\UrlInventoryResource;
use App\Models\SeoOpportunity;
use App\Models\UrlInventory;
use App\Support\CurrentScanResolver;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CurrentScanDiscoverySummary extends BaseWidget
{
    protected function getStats(): array
    {
        $context = CurrentScanResolver::contextForUser(Auth::user());
        $site = $context['site'];
        $scanRun = $context['scan_run'];
        $description = $context['description'];

        if (! $site || ! $scanRun) {
            return [
                Stat::make('URLs in current scan', '0')
                    ->description($description)
                    ->color('gray')
                    ->url(UrlInventoryResource::getUrl('index')),
                Stat::make('Opportunities in current scan', '0')
                    ->description($description)
                    ->color('gray')
                    ->url(SeoOpportunityResource::getUrl('index')),
            ];
        }

        $urlsInScan = UrlInventory::query()
            ->where('site_id', $site->id)
            ->where('last_seen_scan_run_id', $scanRun->id)
            ->count();

        $opportunitiesInScan = SeoOpportunity::query()
            ->where('site_id', $site->id)
            ->where('scan_run_id', $scanRun->id)
            ->count();

        return [
            Stat::make('URLs in current scan', number_format($urlsInScan))
                ->description($description)
                ->descriptionIcon('heroicon-o-globe-alt')
                ->color($urlsInScan > 0 ? 'success' : 'gray')
                ->url(UrlInventoryResource::getUrl('index', ['tableFilters' => ['current_scan' => ['isActive' => true, 'scan_run_id' => $scanRun->id, 'site_id' => $site->id]]])),
            Stat::make('Opportunities in current scan', number_format($opportunitiesInScan))
                ->description($description)
                ->descriptionIcon('heroicon-o-light-bulb')
                ->color($opportunitiesInScan > 0 ? 'warning' : 'gray')
                ->url(SeoOpportunityResource::getUrl('index', ['tableFilters' => ['current_scan' => ['isActive' => true, 'scan_run_id' => $scanRun->id, 'site_id' => $site->id]]])),
        ];
    }
}