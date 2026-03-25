<?php

namespace App\Filament\Widgets;

use App\Models\PageContent;
use App\Models\PageMetadata;
use App\Models\UrlInventory;
use App\Support\CurrentScanResolver;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CrawlHealthWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 4;

    protected function getHeading(): ?string
    {
        $context = CurrentScanResolver::dtoForUser(Auth::user());
        $site = $context->site;

        return 'Page Issues To Review' . ($site ? " for {$site->domain}" : '');
    }

    protected function getStats(): array
    {
        $context = CurrentScanResolver::dtoForUser(Auth::user());
        $site = $context->site;
        $scanRun = $context->metricsScan;
        $siteId = $site?->id;
        $siteLabel = $site?->domain ?? 'the selected site';
        $databaseIssueDetected = false;

        $orphanPages = $this->safeCount(function () use ($siteId, $scanRun) {
            return UrlInventory::query()
                ->when($siteId, fn ($query) => $query->where('site_id', $siteId))
                ->when($scanRun?->id, fn ($query, int $scanRunId) => $query->where('last_seen_scan_run_id', $scanRunId))
                ->where('is_orphan_page', true)
                ->where('indexability_status', 'indexable')
                ->count();
        }, 'orphan_pages', $databaseIssueDetected);

        $missingTitle = $this->safeCount(function () use ($siteId, $scanRun) {
            return PageMetadata::query()
                ->when($siteId, fn ($query) => $query->whereHas('url', function ($urlQuery) use ($siteId, $scanRun): void {
                    $urlQuery->where('site_id', $siteId)
                        ->when($scanRun?->id, fn ($inner, int $scanRunId) => $inner->where('last_seen_scan_run_id', $scanRunId));
                }))
                ->where(function ($query): void {
                    $query->whereNull('title')->orWhere('title', '');
                })
                ->count();
        }, 'missing_title', $databaseIssueDetected);

        $missingH1 = $this->safeCount(function () use ($siteId, $scanRun) {
            return PageMetadata::query()
                ->when($siteId, fn ($query) => $query->whereHas('url', function ($urlQuery) use ($siteId, $scanRun): void {
                    $urlQuery->where('site_id', $siteId)
                        ->when($scanRun?->id, fn ($inner, int $scanRunId) => $inner->where('last_seen_scan_run_id', $scanRunId));
                }))
                ->where(function ($query): void {
                    $query->whereNull('h1')->orWhere('h1', '');
                })
                ->count();
        }, 'missing_h1', $databaseIssueDetected);

        $lowWordCount = $this->safeCount(function () use ($siteId, $scanRun) {
            return PageContent::query()
                ->when($siteId, fn ($query) => $query->whereHas('url', function ($urlQuery) use ($siteId, $scanRun): void {
                    $urlQuery->where('site_id', $siteId)
                        ->when($scanRun?->id, fn ($inner, int $scanRunId) => $inner->where('last_seen_scan_run_id', $scanRunId));
                }))
                ->where('word_count', '<', 250)
                ->count();
        }, 'low_word_count', $databaseIssueDetected);

        $healthSuffix = $databaseIssueDetected ? ' (database issue detected)' : '';

        return [
            Stat::make('Orphan Pages', $orphanPages)
                ->description("Pages search engines can reach, but your site does not link to clearly{$healthSuffix}")
                ->descriptionIcon($orphanPages > 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle')
                ->icon('heroicon-o-link-slash')
                ->color($orphanPages > 0 ? 'warning' : 'success'),

            Stat::make('Pages Missing Title', $missingTitle)
                ->description("Pages missing page titles{$healthSuffix}")
                ->descriptionIcon($missingTitle > 0 ? 'heroicon-o-exclamation-circle' : 'heroicon-o-check-circle')
                ->icon('heroicon-o-tag')
                ->color($missingTitle > 0 ? 'danger' : 'success'),

            Stat::make('Pages Missing H1', $missingH1)
                ->description("Pages missing main page headings{$healthSuffix}")
                ->descriptionIcon($missingH1 > 0 ? 'heroicon-o-exclamation-circle' : 'heroicon-o-check-circle')
                ->icon('heroicon-o-code-bracket')
                ->color($missingH1 > 0 ? 'warning' : 'success'),

            Stat::make('Low Word Count Pages', $lowWordCount)
                ->description("Pages with very little readable content in the selected scan for {$siteLabel}{$healthSuffix}")
                ->descriptionIcon($lowWordCount > 0 ? 'heroicon-o-exclamation-circle' : 'heroicon-o-check-circle')
                ->icon('heroicon-o-document-text')
                ->color($lowWordCount > 0 ? 'warning' : 'success'),
        ];
    }

    protected function safeCount(callable $callback, string $metric, bool &$databaseIssueDetected): int
    {
        try {
            return (int) $callback();
        } catch (QueryException $exception) {
            $databaseIssueDetected = true;

            Log::warning('CrawlHealthWidget query failed', [
                'metric' => $metric,
                'error' => $exception->getMessage(),
            ]);

            return 0;
        }
    }
}
