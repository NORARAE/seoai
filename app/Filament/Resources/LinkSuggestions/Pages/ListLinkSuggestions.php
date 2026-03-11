<?php

namespace App\Filament\Resources\LinkSuggestions\Pages;

use App\Filament\Resources\LinkSuggestions\LinkSuggestionResource;
use Filament\Resources\Pages\ListRecords;

class ListLinkSuggestions extends ListRecords
{
    protected static string $resource = LinkSuggestionResource::class;
}
