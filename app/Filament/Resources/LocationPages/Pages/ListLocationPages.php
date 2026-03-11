<?php

namespace App\Filament\Resources\LocationPages\Pages;

use App\Filament\Resources\LocationPages\LocationPageResource;
use Filament\Resources\Pages\ListRecords;

class ListLocationPages extends ListRecords
{
    protected static string $resource = LocationPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
