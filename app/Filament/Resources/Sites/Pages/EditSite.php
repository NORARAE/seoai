<?php

namespace App\Filament\Resources\Sites\Pages;

use App\Filament\Resources\Sites\SiteResource;
use App\Services\GscSyncService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSite extends EditRecord
{
    protected static string $resource = SiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('openSitemap')
                ->label('Open Sitemap')
                ->url(fn () => $this->record->sitemap_index_url, shouldOpenInNewTab: true),
            Action::make('submitSitemapToGsc')
                ->label('Submit Sitemap to GSC')
                ->requiresConfirmation()
                ->disabled(fn () => ! $this->record->gsc_property_url)
                ->action(function (): void {
                    $result = app(GscSyncService::class)->submitSitemap($this->record, $this->record->sitemap_index_url);

                    $notification = Notification::make()
                        ->title($result['success'] ? 'Sitemap submitted to GSC' : 'Sitemap submission failed')
                        ->body($result['success'] ? $this->record->sitemap_index_url : ($result['error'] ?? 'Unknown error'));

                    if ($result['success']) {
                        $notification->success();
                    } else {
                        $notification->danger();
                    }

                    $notification->send();
                }),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
