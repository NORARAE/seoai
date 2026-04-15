<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\FrontendDevRestricted;
use App\Filament\Widgets\GA4OverviewWidget;
use App\Filament\Widgets\GA4SessionsTrendWidget;
use App\Filament\Widgets\GA4TopPagesWidget;
use App\Filament\Widgets\GA4TrafficSourcesWidget;
use BackedEnum;
use Filament\Pages\Dashboard;

class MarketingIntelligence extends Dashboard
{
    use FrontendDevRestricted;

    protected static string $routePath = '/marketing-intelligence';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Marketing Intelligence';

    protected static ?string $title = 'Marketing Intelligence';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        if (!auth()->user()?->canApproveUsers()) {
            return false;
        }

        // Also apply the FrontendDevRestricted check
        if (\App\Support\FrontendDevAccess::isRestricted()) {
            return \App\Support\FrontendDevAccess::allows(static::class);
        }

        return true;
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        if (!auth()->user()?->canApproveUsers()) {
            return false;
        }

        return parent::shouldRegisterNavigation($parameters);
    }

    public function getWidgets(): array
    {
        return [
            GA4OverviewWidget::class,
            GA4SessionsTrendWidget::class,
            GA4TrafficSourcesWidget::class,
            GA4TopPagesWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return ['default' => 1, 'md' => 12];
    }
}
