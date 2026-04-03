<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;

class BrandSystem extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationLabel = 'Brand System';

    protected static ?string $title = 'Brand System';

    protected static string|\UnitEnum|null $navigationGroup = 'Support';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.brand-system';
}
