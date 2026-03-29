<?php

namespace App\Filament\Widgets;

use App\Services\GoogleAnalyticsService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class GA4TopPagesWidget extends Widget
{
    protected string $view = 'filament.widgets.ga4-top-pages';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public int $days = 28;

    public function getPages(): array
    {
        if (! Auth::user()?->canApproveUsers()) {
            return [];
        }

        return app(GoogleAnalyticsService::class)->fetchTopPages($this->days, 15);
    }
}
