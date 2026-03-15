<?php

namespace App\Filament\Resources\PublishingLogResource\Pages;

use App\Filament\Resources\PublishingLogResource;
use Filament\Resources\Pages\ListRecords;

class ListPublishingLogs extends ListRecords
{
    protected static string $resource = PublishingLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action - logs are generated automatically
        ];
    }
}
