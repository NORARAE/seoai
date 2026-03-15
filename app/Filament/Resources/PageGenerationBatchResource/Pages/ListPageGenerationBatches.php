<?php

namespace App\Filament\Resources\PageGenerationBatchResource\Pages;

use App\Filament\Resources\PageGenerationBatchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPageGenerationBatches extends ListRecords
{
    protected static string $resource = PageGenerationBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
