<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\FrontendDevRestricted;

use BackedEnum;
use Filament\Pages\Page;

class HelpGuides extends Page
{
    use FrontendDevRestricted;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-lifebuoy';

    protected static ?string $navigationLabel = 'Help & Guides';

    protected static ?string $title = 'Help & Guides';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 13;

    protected string $view = 'filament.pages.help-guides';
}
