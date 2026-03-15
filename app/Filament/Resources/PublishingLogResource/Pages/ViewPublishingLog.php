<?php

namespace App\Filament\Resources\PublishingLogResource\Pages;

use App\Filament\Resources\PublishingLogResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPublishingLog extends ViewRecord
{
    protected static string $resource = PublishingLogResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Log Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('payload.title')
                            ->label('Page Payload')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                        
                        Infolists\Components\TextEntry::make('payload.site.name')
                            ->label('Site'),
                        
                        Infolists\Components\TextEntry::make('adapter_type')
                            ->label('Adapter')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'wordpress' => 'primary',
                                'export' => 'warning',
                                default => 'gray',
                            }),
                        
                        Infolists\Components\TextEntry::make('action')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'publish' => 'success',
                                'export' => 'warning',
                                'update' => 'info',
                                'delete' => 'danger',
                                default => 'gray',
                            }),
                        
                        Infolists\Components\TextEntry::make('result')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'success' => 'success',
                                'failed' => 'danger',
                                default => 'gray',
                            })
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Publishing Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('remote_url')
                            ->label('Remote URL')
                            ->url(fn ($record) => $record->remote_url, shouldOpenInNewTab: true)
                            ->placeholder('Not available')
                            ->columnSpanFull(),
                        
                        Infolists\Components\TextEntry::make('error_message')
                            ->label('Error Message')
                            ->color('danger')
                            ->placeholder('No errors')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Remote Response')
                    ->schema([
                        Infolists\Components\TextEntry::make('remote_response')
                            ->label('')
                            ->markdown()
                            ->placeholder('No response data')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Infolists\Components\Section::make('Timing')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                    ]),
            ]);
    }
}
