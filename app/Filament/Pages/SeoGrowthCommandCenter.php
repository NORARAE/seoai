<?php

namespace App\Filament\Pages;

use App\Filament\Resources\SeoOpportunityResource;
use App\Filament\Resources\Sites\SiteResource;
use App\Filament\Widgets\CompetitorGapWidget;
use App\Filament\Widgets\CrawlHealthWidget;
use App\Filament\Widgets\DiscoveryQueueWidget;
use App\Filament\Widgets\PlanAndLimitsPanelWidget;
use App\Filament\Widgets\ScanLifecycleStripWidget;
use App\Filament\Widgets\SeoOpportunitiesSummaryWidget;
use App\Filament\Widgets\SiteScanSummaryWidget;
use App\Filament\Widgets\TopOpportunitiesWidget;
use App\Filament\Widgets\UrlInventorySummaryWidget;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Dashboard;
use Illuminate\Support\Facades\Auth;

class SeoGrowthCommandCenter extends Dashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Command Center';

    protected static ?string $title = 'SEO Growth Command Center';

    protected static ?int $navigationSort = 1;

    public function getHeading(): string
    {
        return 'SEO Growth Command Center';
    }

    public function getSubheading(): ?string
    {
        return 'Select a site, run or resume a scan, then review discovered pages, scan progress, and recommended SEO actions from one workspace.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('add_site')
                ->label('Add Site')
                ->icon('heroicon-o-plus')
                ->color('gray')
                ->url(SiteResource::getUrl('create', array_filter([
                    'client_id' => Auth::user()?->client_id,
                ]))),
            Action::make('manage_sites')
                ->label('Manage Sites')
                ->icon('heroicon-o-rectangle-stack')
                ->color('gray')
                ->url(SiteResource::getUrl()),
            Action::make('review_opportunities')
                ->label('Review Opportunities')
                ->icon('heroicon-o-light-bulb')
                ->color('primary')
                ->url(SeoOpportunityResource::getUrl()),
            Action::make('help_guides')
                ->label('Help & Guides')
                ->icon('heroicon-o-lifebuoy')
                ->url(HelpGuides::getUrl()),
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'md' => 12,
            'xl' => 12,
        ];
    }

    public function getWidgets(): array
    {
        return [
            SiteScanSummaryWidget::class,
            ScanLifecycleStripWidget::class,
            DiscoveryQueueWidget::class,
            UrlInventorySummaryWidget::class,
            SeoOpportunitiesSummaryWidget::class,
            TopOpportunitiesWidget::class,
            CrawlHealthWidget::class,
            CompetitorGapWidget::class,
            PlanAndLimitsPanelWidget::class,
        ];
    }
}
