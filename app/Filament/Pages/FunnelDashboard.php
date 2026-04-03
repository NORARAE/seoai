<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\FrontendDevRestricted;
use App\Filament\Widgets\FunnelConversionWidget;
use BackedEnum;
use Filament\Pages\Dashboard;

class FunnelDashboard extends Dashboard
{
    use FrontendDevRestricted;

    protected static string $routePath = '/funnel-dashboard';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static ?string $navigationLabel = 'Funnel Dashboard';

    protected static ?string $title = 'Funnel Dashboard';

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 7;

    public static function canAccess(): bool
    {
        if (!auth()->user()?->canApproveUsers()) {
            return false;
        }

        if (\App\Support\FrontendDevAccess::isRestricted()) {
            return \App\Support\FrontendDevAccess::allows(static::class);
        }

        return true;
    }

    public function getWidgets(): array
    {
        return [
            FunnelConversionWidget::class,
        ];
    }
}
