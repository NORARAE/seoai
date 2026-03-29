<?php

namespace App\Filament\Widgets;

use App\Services\GoogleAnalyticsService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class GA4TrafficSourcesWidget extends Widget
{
    protected string $view = 'filament.widgets.ga4-traffic-sources';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public int $days = 28;

    public function getSources(): array
    {
        if (! Auth::user()?->canApproveUsers()) {
            return [];
        }

        return app(GoogleAnalyticsService::class)->fetchTrafficSources($this->days);
    }
}
