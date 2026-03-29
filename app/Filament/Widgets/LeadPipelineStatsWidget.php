<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class LeadPipelineStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getHeading(): ?string
    {
        return 'Client Pipeline — Stage Breakdown';
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

        return [
            Stat::make('Total Leads', number_format($total))
                ->description('All pipeline entries')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('gray'),

            Stat::make('Booked', number_format($get(Lead::STAGE_BOOKED)))
                ->description('Session booked, awaiting payment')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('gray'),

            Stat::make('Paid', number_format($get(Lead::STAGE_PAID)))
                ->description('Payment received, onboarding not started')
                ->descriptionIcon('heroicon-o-credit-card')
                ->color('warning'),

            Stat::make('Onboarding Submitted', number_format($get(Lead::STAGE_ONBOARDING_SUBMITTED)))
                ->description('Form submitted, awaiting admin review')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('info'),

            Stat::make('Approved', number_format($get(Lead::STAGE_APPROVED)))
                ->description('Approved — not yet activated')
                ->descriptionIcon('heroicon-o-check-badge')
                ->color('success'),

            Stat::make('Active Clients', number_format($get(Lead::STAGE_ACTIVE)))
                ->description('Currently active and engaged')
                ->descriptionIcon('heroicon-o-bolt')
                ->color('success'),

            Stat::make('Rejected / Lost', number_format($get(Lead::STAGE_REJECTED) + $get(Lead::STAGE_LOST)))
                ->description('Rejected by admin or churned')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
