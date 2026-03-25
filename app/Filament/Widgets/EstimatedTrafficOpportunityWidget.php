<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SeoOpportunityResource;
use App\Models\SeoOpportunity;
use App\Services\OpportunityScoreService;
use App\Support\CurrentScanResolver;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class EstimatedTrafficOpportunityWidget extends Widget
{
    protected string $view = 'filament.widgets.estimated-traffic-opportunity-widget';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 1;

    protected function getViewData(): array
    {
        $context = CurrentScanResolver::contextForUser(Auth::user());
        $site = $context['site'];
        $scanRun = $context['scan_run'];
        $siteId = $site?->id;

        $opportunities = SeoOpportunity::query()
            ->when($siteId, fn ($q) => $q->where('site_id', $siteId))
            ->when($scanRun?->id, fn ($q, int $scanRunId) => $q->where('scan_run_id', $scanRunId))
            ->when(! $scanRun && $siteId, fn ($q) => $q->whereRaw('1 = 0'))
            ->whereIn('status', ['pending', 'approved', 'in_progress'])
            ->get();

        $scoring = app(OpportunityScoreService::class);

        $trafficRows = $opportunities->map(function (SeoOpportunity $opportunity) use ($scoring): array {
            $volume = (int) ($opportunity->search_volume ?? data_get($opportunity->keyword_data, 'search_volume', 0));

            if ($volume <= 0) {
                $volume = max(50, (int) round(((float) ($opportunity->priority_score ?? 40)) * 12));
            }

            $position = $opportunity->current_position ?: 3;
            $estimated = $scoring->estimateMonthlyTrafficPotential($volume, (int) $position);

            return [
                'opportunity' => $opportunity,
                'estimated' => $estimated,
            ];
        })->sortByDesc('estimated')->values();

        $total = (int) $trafficRows->sum('estimated');
        $top = $trafficRows->first();

        return [
            'domain' => $site?->domain,
            'estimatedMonthlyVisits' => $total,
            'topKeyword' => data_get($top, 'opportunity.target_keyword') ?? data_get($top, 'opportunity.suggested_url') ?? 'No top opportunity yet',
            'topEstimatedVisits' => (int) (data_get($top, 'estimated') ?? 0),
            'opportunitiesUrl' => $scanRun
                ? SeoOpportunityResource::getUrl('index', ['tableFilters' => ['current_scan' => ['isActive' => true, 'site_id' => $siteId, 'scan_run_id' => $scanRun->id]]])
                : SeoOpportunityResource::getUrl(),
            'generatePagesUrl' => $scanRun
                ? SeoOpportunityResource::getUrl('index', ['tableFilters' => ['current_scan' => ['isActive' => true, 'site_id' => $siteId, 'scan_run_id' => $scanRun->id]]])
                : SeoOpportunityResource::getUrl(),
        ];
    }
}
