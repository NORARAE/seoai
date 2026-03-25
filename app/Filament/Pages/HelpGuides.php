<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;

class HelpGuides extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-lifebuoy';

    protected static ?string $navigationLabel = 'Help & Guides';

    protected static ?string $title = 'Help & Guides';

    protected static string|\UnitEnum|null $navigationGroup = 'Support';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.help-guides';
}
