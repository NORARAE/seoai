<?php

namespace App\Filament\Resources\PagePayloadResource\Pages;

use App\Filament\Resources\PagePayloadResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPagePayload extends ViewRecord
{
    protected static string $resource = PagePayloadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
