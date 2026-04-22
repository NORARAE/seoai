<?php

namespace App\Filament\Resources\AiChatLogResource\Pages;

use App\Filament\Resources\AiChatLogResource;
use App\Filament\Widgets\AiTopQuestionsWidget;
use Filament\Resources\Pages\ListRecords;

class ListAiChatLogs extends ListRecords
{
    protected static string $resource = AiChatLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AiTopQuestionsWidget::class,
        ];
    }
}
