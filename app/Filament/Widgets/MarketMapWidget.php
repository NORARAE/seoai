<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SeoOpportunityResource;
use App\Models\City;
use App\Models\SeoOpportunity;
use App\Models\Service;
use App\Models\ServiceLocation;
use App\Support\CurrentScanResolver;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class MarketMapWidget extends Widget
{
    protected string $view = 'filament.widgets.market-map-widget';

    protected static ?int $sort = 9;

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $context = CurrentScanResolver::contextForUser(Auth::user());
        $site = $context['site'];
        $scanRun = $context['scan_run'];
        $stateId = $site?->state_id;

        $services = Service::query()->where('is_active', true)->orderBy('name')->limit(6)->get(['id', 'name', 'slug']);
        $cities = City::query()->when($stateId, fn ($q) => $q->where('state_id', $stateId))->orderBy('name')->limit(6)->get(['id', 'name', 'slug']);

        $matrix = [];

        foreach ($services as $service) {
            $row = [];

            foreach ($cities as $city) {
                $serviceLocation = ServiceLocation::query()
                    ->where('service_id', $service->id)
                    ->where('city_id', $city->id)
                    ->first();

                $opportunity = SeoOpportunity::query()
                    ->when($site?->id, fn ($q) => $q->where('site_id', $site->id))
                    ->when($scanRun?->id, fn ($q, int $scanRunId) => $q->where('scan_run_id', $scanRunId))
                    ->where('service_id', $service->id)
                    ->where('location_id', $city->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->first();

                $status = 'missing';

                if ($serviceLocation?->page_exists) {
                    $status = 'covered';
                } elseif ($opportunity) {
                    $status = 'opportunity';
                }

                $row[] = [
                    'service' => $service->name,
                    'location' => $city->name,
                    'status' => $status,
                    'searchVolume' => (int) ($opportunity?->search_volume ?? 0),
                    'competitorEvidence' => data_get($opportunity?->competitor_analysis, 'competitor_domain'),
                    'opportunitiesUrl' => SeoOpportunityResource::getUrl(),
                ];
            }

            $matrix[] = [
                'service' => $service->name,
                'cells' => $row,
            ];
        }

        return [
            'domain' => $site?->domain,
            'cities' => $cities,
            'matrix' => $matrix,
        ];
    }
}
