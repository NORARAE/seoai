<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\BuildsScanScopedLinks;
use App\Filament\Resources\SeoOpportunityResource;
use App\Models\SeoOpportunity;
use App\Support\CurrentScanResolver;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SeoOpportunitiesSummaryWidget extends BaseWidget
{
    use BuildsScanScopedLinks;

    protected int | string | array $columnSpan = 4;

    protected static ?int $sort = 4;

    protected function getHeading(): ?string
    {
        return 'Opportunities To Review';
    }

    protected function getDescription(): ?string
    {
        $context = CurrentScanResolver::dtoForUser(Auth::user());

        if (! $context->siteId()) {
            return 'Choose a site to see the next SEO actions.';
        }

        if (! $context->scanRunId()) {
            return 'Finish a site scan to unlock opportunities for review.';
        }

        return 'These opportunities come from the selected site scan.';
    }

    protected function getStats(): array
    {
        $context = CurrentScanResolver::dtoForUser(Auth::user());

        if (! $context->siteId() || ! $context->scanRunId()) {
            return [
                Stat::make('Opportunities', '0')
                    ->description('Finish a site scan to unlock opportunities for review.')
                    ->color('gray')
                    ->url(SeoOpportunityResource::getUrl()),
            ];
        }

        $base = SeoOpportunity::query()
            ->where('site_id', $context->siteId())
            ->where('scan_run_id', $context->scanRunId());

        return [
            Stat::make('Needs Review', (string) (clone $base)->where('status', 'pending')->count())
                ->description('Review these first')
                ->color('warning')
                ->url($this->scanScopedUrl(SeoOpportunityResource::class, $context)),
            Stat::make('Ready To Build', (string) (clone $base)->where('status', 'approved')->count())
                ->description('Approved and ready for page generation')
                ->color('success')
                ->url($this->scanScopedUrl(SeoOpportunityResource::class, $context)),
            Stat::make('In progress', (string) (clone $base)->where('status', 'in_progress')->count())
                ->description('Already being worked on')
                ->color('primary')
                ->url($this->scanScopedUrl(SeoOpportunityResource::class, $context)),
        ];
    }
}