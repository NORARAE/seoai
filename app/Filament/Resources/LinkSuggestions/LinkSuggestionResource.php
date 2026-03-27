<?php

namespace App\Filament\Resources\LinkSuggestions;

use App\Filament\Concerns\FrontendDevRestricted;

use App\Filament\Resources\LinkSuggestions\Pages\ListLinkSuggestions;
use App\Filament\Resources\LinkSuggestions\Pages\ViewLinkSuggestion;
use App\Filament\Resources\LinkSuggestions\Tables\LinkSuggestionsTable;
use App\Models\LinkSuggestion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LinkSuggestionResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = LinkSuggestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static ?string $navigationLabel = 'Link Suggestions';

    protected static ?int $navigationSort = 4;

    public static function table(Table $table): Table
    {
        return LinkSuggestionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLinkSuggestions::route('/'),
            'view' => ViewLinkSuggestion::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
