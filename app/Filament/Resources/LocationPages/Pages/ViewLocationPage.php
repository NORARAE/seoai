<?php

namespace App\Filament\Resources\LocationPages\Pages;

use App\Filament\Resources\LocationPages\LocationPageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLocationPage extends ViewRecord
{
    protected static string $resource = LocationPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
