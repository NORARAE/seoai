<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Sites\SiteResource;
use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('addSite')
                ->label('Add Site')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(fn (): string => SiteResource::getUrl('create', ['client_id' => $this->record->id])),
            Actions\EditAction::make(),
        ];
    }
}
