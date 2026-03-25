<?php

namespace App\Filament\Widgets;

use App\Models\CompetitorDomain;
use App\Models\CompetitorScanRun;
use App\Support\CurrentScanResolver;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ScanContextHeaderWidget extends Widget
{
    protected string $view = 'filament.widgets.scan-context-header-widget';

    protected int | string | array $columnSpan = 12;

    protected function getViewData(): array
    {
        $tableFilters = request()->input('tableFilters', []);
        $siteId = (int) data_get($tableFilters, 'current_scan.site_id');
        $scanRunId = (int) data_get($tableFilters, 'current_scan.scan_run_id');
        $competitorScanRunId = (int) data_get($tableFilters, 'current_competitor.competitor_scan_run_id');
        $competitorDomainId = (int) data_get($tableFilters, 'current_competitor.competitor_domain_id');

        $context = CurrentScanResolver::dtoForUser(
            Auth::user(),
            $siteId > 0 ? $siteId : null,
            $scanRunId > 0 ? $scanRunId : null,
        );

        $competitorScanRun = $competitorScanRunId > 0
            ? CompetitorScanRun::query()->with('competitorDomain')->find($competitorScanRunId)
            : null;

        $competitorDomain = $competitorScanRun?->competitorDomain
            ?? ($competitorDomainId > 0 ? CompetitorDomain::query()->find($competitorDomainId) : null);

        return [
            'context' => $context,
            'hasRequestedCurrentScan' => $siteId > 0 || $scanRunId > 0,
            'hasResolvedCurrentScan' => $siteId > 0
                && $scanRunId > 0
                && $context->siteId() === $siteId
                && $context->scanRunId() === $scanRunId,
            'competitorScanRun' => $competitorScanRun,
            'competitorDomain' => $competitorDomain,
        ];
    }
}