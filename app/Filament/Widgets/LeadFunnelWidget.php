<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class LeadFunnelWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getHeading(): ?string
    {
        return 'Booking Funnel Conversion Rates';
    }

    protected function getDescription(): ?string
    {
        return 'Cumulative: each % reflects how many leads reached that stage or beyond.';
    }

    protected function getStats(): array
    {
        if (! Auth::user()?->canApproveUsers()) {
            return [];
        }

        $counts = Lead::query()
            ->selectRaw('lifecycle_stage, count(*) as count')
            ->groupBy('lifecycle_stage')
            ->pluck('count', 'lifecycle_stage');

        $get = fn (string $stage): int => (int) ($counts[$stage] ?? 0);

        $total = Lead::count();

        // Cumulative: how many leads reached each stage OR progressed past it
        $reachedBooked = $total - $get(Lead::STAGE_NEW);
        $reachedPaid   = $get(Lead::STAGE_PAID)
            + $get(Lead::STAGE_ONBOARDING_SUBMITTED)
            + $get(Lead::STAGE_APPROVED)
            + $get(Lead::STAGE_ACTIVE);
        $reachedOnboarding = $get(Lead::STAGE_ONBOARDING_SUBMITTED)
            + $get(Lead::STAGE_APPROVED)
            + $get(Lead::STAGE_ACTIVE);
        $reachedActive = $get(Lead::STAGE_ACTIVE);

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
            Stat::make('Enquiry → Booked', $pct($reachedBooked, $total))
                ->description("{$reachedBooked} of {$total} total leads booked a session")
                ->color($color($reachedBooked, $total)),

            Stat::make('Booked → Paid', $pct($reachedPaid, $reachedBooked))
                ->description("{$reachedPaid} of {$reachedBooked} booked leads paid")
                ->color($color($reachedPaid, $reachedBooked)),

            Stat::make('Paid → Onboarding', $pct($reachedOnboarding, $reachedPaid))
                ->description("{$reachedOnboarding} of {$reachedPaid} paid leads submitted onboarding")
                ->color($color($reachedOnboarding, $reachedPaid)),

            Stat::make('Onboarding → Active', $pct($reachedActive, $reachedOnboarding))
                ->description("{$reachedActive} of {$reachedOnboarding} onboarded leads became active")
                ->color($color($reachedActive, $reachedOnboarding)),
        ];
    }
}
