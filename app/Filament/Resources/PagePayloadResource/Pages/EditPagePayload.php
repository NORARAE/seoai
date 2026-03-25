<?php

namespace App\Filament\Resources\PagePayloadResource\Pages;

use App\Filament\Resources\PagePayloadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPagePayload extends EditRecord
{
    protected static string $resource = PagePayloadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Payload metadata updated';
    }
}
