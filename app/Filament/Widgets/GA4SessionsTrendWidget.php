<?php

namespace App\Filament\Widgets;

use App\Services\GoogleAnalyticsService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class GA4SessionsTrendWidget extends ChartWidget
{
    protected ?string $heading = 'Sessions Trend';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public int $days = 28;

    protected function getData(): array
    {
        if (! Auth::user()?->canApproveUsers()) {
            return ['datasets' => [], 'labels' => []];
        }

        $series = app(GoogleAnalyticsService::class)->fetchSessionsSeries($this->days);

        if (empty($series)) {
            return ['datasets' => [], 'labels' => []];
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Sessions',
                    'data'            => array_column($series, 'sessions'),
                    'borderColor'     => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.3,
                    'pointRadius'     => 3,
                ],
            ],
            'labels' => array_column($series, 'date'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
