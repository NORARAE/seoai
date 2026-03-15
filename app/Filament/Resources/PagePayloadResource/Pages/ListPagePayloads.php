<?php

namespace App\Filament\Resources\PagePayloadResource\Pages;

use App\Filament\Resources\PagePayloadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPagePayloads extends ListRecords
{
    protected static string $resource = PagePayloadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Payloads are generated via batches, not created manually
        ];
    }
}
