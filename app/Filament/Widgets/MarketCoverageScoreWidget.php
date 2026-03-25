<?php

namespace App\Filament\Widgets;

use App\Models\CompetitorGap;
use App\Models\SeoOpportunity;
use App\Models\ServiceLocation;
use App\Models\UrlInventory;
use App\Services\MarketCaptureMode;
use App\Support\CurrentScanResolver;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class MarketCoverageScoreWidget extends Widget
{
    protected string $view = 'filament.widgets.market-coverage-score-widget';

    protected static ?int $sort = 8;

    protected int | string | array $columnSpan = 1;

    protected function getViewData(): array
    {
        $context = CurrentScanResolver::contextForUser(Auth::user());
        $site = $context['site'];
        $scanRun = $context['scan_run'];
        $stateId = $site?->state_id;

        $serviceTotal = ServiceLocation::query()->when($stateId, fn ($q) => $q->where('state_id', $stateId))->count();
        $serviceCovered = ServiceLocation::query()->when($stateId, fn ($q) => $q->where('state_id', $stateId))->where('page_exists', true)->count();
        $serviceCoverage = $serviceTotal > 0 ? (int) round(($serviceCovered / $serviceTotal) * 100) : 0;

        $locationTotal = ServiceLocation::query()->when($stateId, fn ($q) => $q->where('state_id', $stateId))->distinct('city_id')->count('city_id');
        $locationCovered = ServiceLocation::query()->when($stateId, fn ($q) => $q->where('state_id', $stateId))->where('page_exists', true)->distinct('city_id')->count('city_id');
        $locationCoverage = $locationTotal > 0 ? (int) round(($locationCovered / $locationTotal) * 100) : 0;

        $indexablePages = UrlInventory::query()
            ->when($scanRun?->id, fn ($q, int $scanRunId) => $q->where('last_seen_scan_run_id', $scanRunId))
            ->where('indexability_status', 'indexable')
            ->count();
        $linkedPages = UrlInventory::query()
            ->when($scanRun?->id, fn ($q, int $scanRunId) => $q->where('last_seen_scan_run_id', $scanRunId))
            ->where('indexability_status', 'indexable')
            ->where('incoming_link_count', '>', 0)
            ->count();
        $internalLinkCoverage = $indexablePages > 0 ? (int) round(($linkedPages / $indexablePages) * 100) : 0;

        $gapTotal = CompetitorGap::query()
            ->when($site?->id, fn ($q) => $q->where('site_id', $site->id))
            ->when($scanRun?->id, fn ($q, int $scanRunId) => $q->where('site_scan_run_id', $scanRunId))
            ->when(! $scanRun && $site?->id, fn ($q) => $q->whereRaw('1 = 0'))
            ->where('is_current', true)
            ->count();
        $gapOpen = CompetitorGap::query()
            ->when($site?->id, fn ($q) => $q->where('site_id', $site->id))
            ->when($scanRun?->id, fn ($q, int $scanRunId) => $q->where('site_scan_run_id', $scanRunId))
            ->when(! $scanRun && $site?->id, fn ($q) => $q->whereRaw('1 = 0'))
            ->where('is_current', true)
            ->whereIn('status', ['open', 'queued'])
            ->count();
        $competitorGapCoverage = $gapTotal > 0 ? max(0, (int) round(100 - (($gapOpen / $gapTotal) * 100))) : 100;

        $coverageScore = (int) round(($serviceCoverage * 0.30) + ($locationCoverage * 0.30) + ($internalLinkCoverage * 0.20) + ($competitorGapCoverage * 0.20));

        $mode = app(MarketCaptureMode::class)->forCoverageScore($coverageScore);

        return [
            'coverageScore' => $coverageScore,
            'serviceCoverage' => $serviceCoverage,
            'locationCoverage' => $locationCoverage,
            'internalLinkCoverage' => $internalLinkCoverage,
            'competitorGapCoverage' => $competitorGapCoverage,
            'mode' => $mode,
            'domain' => $site?->domain,
        ];
    }
}
