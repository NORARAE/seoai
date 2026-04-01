<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;

class SiteMap extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Page Index';

    protected static ?string $title = 'Page Index';

    protected static string|\UnitEnum|null $navigationGroup = 'Site Management';

    protected static ?int $navigationSort = 1;

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
