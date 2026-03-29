<?php

namespace App\Filament\Widgets;

use App\Services\GoogleAnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class GA4OverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public int $days = 28;

    protected function getHeading(): ?string
    {
        return "GA4 Overview — Last {$this->days} Days";
    }

    protected function getStats(): array
    {
        if (! Auth::user()?->canApproveUsers()) {
            return [];
        }

        $ga4      = app(GoogleAnalyticsService::class);
        $overview = $ga4->fetchOverview($this->days);
        $organic  = $ga4->getOrganicPercentage($this->days);

        // If all zeros, GA4 is not configured or returning no data
        if ($overview['sessions'] === 0 && $overview['users'] === 0 && $overview['pageviews'] === 0) {
            return [
                Stat::make('GA4 Not Connected', '—')
                    ->description('Set GA4_PROPERTY_ID + GOOGLE_APPLICATION_CREDENTIALS in .env to see live data')
                    ->descriptionIcon('heroicon-o-exclamation-triangle')
                    ->color('warning'),
            ];
        }

        $avgDuration = gmdate('i:s', (int) $overview['avg_session_duration']);

        return [
            Stat::make('Sessions', number_format($overview['sessions']))
                ->description("Last {$this->days} days")
                ->descriptionIcon('heroicon-o-cursor-arrow-rays')
                ->color('primary'),

            Stat::make('Users', number_format($overview['users']))
                ->description("Last {$this->days} days")
                ->descriptionIcon('heroicon-o-users')
                ->color('info'),

            Stat::make('Pageviews', number_format($overview['pageviews']))
                ->description("Last {$this->days} days")
                ->descriptionIcon('heroicon-o-eye')
                ->color('gray'),

            Stat::make('Bounce Rate', $overview['bounce_rate'] . '%')
                ->description($overview['bounce_rate'] <= 50 ? 'Healthy' : 'High — review landing pages')
                ->descriptionIcon('heroicon-o-arrow-uturn-left')
                ->color($overview['bounce_rate'] <= 50 ? 'success' : 'warning'),

            Stat::make('Avg. Session', $avgDuration)
                ->description('Average session duration (mm:ss)')
                ->descriptionIcon('heroicon-o-clock')
                ->color('gray'),

            Stat::make('Organic Traffic', $organic . '%')
                ->description('Sessions from organic search')
                ->descriptionIcon('heroicon-o-magnifying-glass')
                ->color($organic >= 30 ? 'success' : 'warning'),
        ];
    }
}
