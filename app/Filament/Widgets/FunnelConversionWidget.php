<?php

namespace App\Filament\Widgets;

use App\Models\FunnelEvent;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class FunnelConversionWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getHeading(): ?string
    {
        return 'Funnel Conversion Metrics';
    }

    protected function getDescription(): ?string
    {
        return 'Event-level tracking: Landing → Onboarding → Booking → Paid';
    }

    protected function getStats(): array
    {
        if (!Auth::user()?->canApproveUsers()) {
            return [];
        }

        $counts = FunnelEvent::query()
            ->selectRaw('event_name, count(*) as count')
            ->groupBy('event_name')
            ->pluck('count', 'event_name');

        $get = fn(string $event): int => (int) ($counts[$event] ?? 0);

        $started = $get(FunnelEvent::ONBOARDING_STARTED);
        $completed = $get(FunnelEvent::ONBOARDING_COMPLETED);
        $created = $get(FunnelEvent::BOOKING_CREATED);
        $paid = $get(FunnelEvent::BOOKING_PAID);

        $pct = function (int $num, int $den): string {
            return $den > 0 ? round($num / $den * 100, 1) . '%' : '—';
        };

        $color = function (int $num, int $den): string {
            if ($den === 0) {
                return 'gray';
            }

            return ($num / $den * 100) >= 50 ? 'success' : 'warning';
        };

        return [
            Stat::make('Onboarding Started', number_format($started))
                ->description('Unique visits to /onboarding/start that fired the event')
                ->color('primary'),

            Stat::make('Onboarding Completed', number_format($completed))
                ->description('Submitted form → ' . $pct($completed, $started) . ' of started')
                ->color($color($completed, $started)),

            Stat::make('Booking Created', number_format($created))
                ->description('Reached booking step → ' . $pct($created, $completed) . ' of completed')
                ->color($color($created, $completed)),

            Stat::make('Booking Paid', number_format($paid))
                ->description('Completed payment → ' . $pct($paid, $created) . ' of created')
                ->color($color($paid, $created)),
        ];
    }
}
