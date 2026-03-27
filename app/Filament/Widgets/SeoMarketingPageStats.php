<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SeoMarketingPageResource;
use App\Models\SeoMarketingPage;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SeoMarketingPageStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $total         = SeoMarketingPage::count();
        $moneyPages    = SeoMarketingPage::whereNotNull('money_page_rank')->count();
        $missingSchema = SeoMarketingPage::where(function ($q) {
            $q->whereNull('schema_json')
              ->orWhere('schema_json', '[]')
              ->orWhere('schema_json', 'null');
        })->count();
        $missingLinks  = SeoMarketingPage::where(function ($q) {
            $q->whereNull('internal_links')
              ->orWhere('internal_links', '[]')
              ->orWhere('internal_links', 'null');
        })->count();
        $missingMeta   = SeoMarketingPage::where(function ($q) {
            $q->whereNull('meta_title')
              ->orWhere('meta_title', '')
              ->orWhereNull('meta_description')
              ->orWhere('meta_description', '');
        })->count();

        return [
            Stat::make('Total SEO Pages', $total)
                ->description('Across 5 content clusters')
                ->descriptionIcon('heroicon-o-globe-alt')
                ->color('primary')
                ->url(SeoMarketingPageResource::getUrl('index')),

            Stat::make('Money Pages', $moneyPages)
                ->description('Highest-priority conversion pages')
                ->descriptionIcon('heroicon-o-star')
                ->color('warning')
                ->url(SeoMarketingPageResource::getUrl('index', [
                    'tableFilters' => ['money_pages_only' => ['isActive' => true]],
                ])),

            Stat::make('Missing Schema', $missingSchema)
                ->description('No stored JSON-LD')
                ->descriptionIcon('heroicon-o-code-bracket')
                ->color($missingSchema > 0 ? 'warning' : 'success')
                ->url(SeoMarketingPageResource::getUrl('index', [
                    'tableFilters' => ['missing_schema' => ['isActive' => true]],
                ])),

            Stat::make('Missing Internal Links', $missingLinks)
                ->description('No link data stored')
                ->descriptionIcon('heroicon-o-link-slash')
                ->color($missingLinks > 0 ? 'warning' : 'success')
                ->url(SeoMarketingPageResource::getUrl('index', [
                    'tableFilters' => ['missing_internal_links' => ['isActive' => true]],
                ])),

            Stat::make('Missing Metadata', $missingMeta)
                ->description('No meta title or description')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($missingMeta > 0 ? 'danger' : 'success')
                ->url(SeoMarketingPageResource::getUrl('index', [
                    'tableFilters' => ['missing_meta_title' => ['isActive' => true]],
                ])),
        ];
    }
}
