<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\CoverageMap;
use App\Models\ServiceLocation;
use App\Support\ActiveSiteContext;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class CoverageMapWidget extends Widget
{
    protected string $view = 'filament.widgets.coverage-map-widget';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 1;

    protected function getViewData(): array
    {
        $site = ActiveSiteContext::resolveForUser(Auth::user());
        $stateId = $site?->state_id;

        $existing = ServiceLocation::query()
            ->when($stateId, fn ($query) => $query->where('state_id', $stateId))
            ->where('page_exists', true)
            ->count();
        $missing = ServiceLocation::query()
            ->when($stateId, fn ($query) => $query->where('state_id', $stateId))
            ->where('page_exists', false)
            ->count();
        $generated = ServiceLocation::query()
            ->when($stateId, fn ($query) => $query->where('state_id', $stateId))
            ->whereNotNull('page_generated_at')
            ->count();

        $total = max(1, $existing + $missing);

        return [
            'existing' => $existing,
            'missing' => $missing,
            'generated' => $generated,
            'existingPct' => (int) round(($existing / $total) * 100),
            'missingPct' => (int) round(($missing / $total) * 100),
            'coverageMapUrl' => CoverageMap::getUrl(),
            'activeSiteDomain' => $site?->domain,
        ];
    }
}
