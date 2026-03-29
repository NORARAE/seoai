<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\FrontendDevRestricted;
use App\Filament\Widgets\LeadFunnelWidget;
use App\Filament\Widgets\LeadPipelineStatsWidget;
use BackedEnum;
use Filament\Pages\Dashboard;

class ClientPipeline extends Dashboard
{
    use FrontendDevRestricted;

    protected static string $routePath = '/client-pipeline';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-funnel';

    protected static ?string $navigationLabel = 'Client Pipeline';

    protected static ?string $title = 'Client Pipeline';

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 6;

    public static function canAccess(): bool
    {
        if (! auth()->user()?->canApproveUsers()) {
            return false;
        }

        // Delegate to FrontendDevRestricted (checks frontend_dev role)
        if (\App\Support\FrontendDevAccess::isRestricted()) {
            return \App\Support\FrontendDevAccess::allows(static::class);
        }

        return true;
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        if (! auth()->user()?->canApproveUsers()) {
            return false;
        }

        return parent::shouldRegisterNavigation($parameters);
    }

    public function getWidgets(): array
    {
        return [
            LeadPipelineStatsWidget::class,
            LeadFunnelWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return ['default' => 1, 'md' => 12];
    }
}
