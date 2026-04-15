<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;

class SiteMap extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Page Index';

    protected static ?string $title = 'Page Index';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 12;

    protected string $view = 'filament.pages.site-map';

    public static function canAccess(): bool
    {
        return auth()->user()?->canApproveUsers() ?? false;
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        if (!auth()->user()?->canApproveUsers()) {
            return false;
        }

        return parent::shouldRegisterNavigation($parameters);
    }
}
