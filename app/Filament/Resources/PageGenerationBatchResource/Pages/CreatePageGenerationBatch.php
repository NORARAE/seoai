<?php

namespace App\Filament\Resources\PageGenerationBatchResource\Pages;

use App\Filament\Resources\PageGenerationBatchResource;
use App\Services\BulkPageExpansionService;
use Filament\Resources\Pages\CreateRecord;

class CreatePageGenerationBatch extends CreateRecord
{
    protected static string $resource = PageGenerationBatchResource::class;

    protected function afterCreate(): void
    {
        // Dispatch generation jobs after batch is created
        $service = app(BulkPageExpansionService::class);
        $service->dispatchGenerationJobs($this->record);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
