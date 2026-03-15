<?php

namespace App\Filament\Resources\PageGenerationBatchResource\Pages;

use App\Filament\Resources\PageGenerationBatchResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPageGenerationBatch extends ViewRecord
{
    protected static string $resource = PageGenerationBatchResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Batch Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                        
                        Infolists\Components\TextEntry::make('description')
                            ->columnSpanFull(),
                        
                        Infolists\Components\TextEntry::make('site.name'),
                        
                        Infolists\Components\TextEntry::make('opportunity_source')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'scan' => 'success',
                                'scheduled' => 'info',
                                default => 'gray',
                            }),
                        
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'processing' => 'primary',
                                'completed' => 'success',
                                'failed' => 'danger',
                                default => 'secondary',
                            }),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Progress')
                    ->schema([
                        Infolists\Components\TextEntry::make('requested_count')
                            ->label('Requested'),
                        
                        Infolists\Components\TextEntry::make('payload_count')
                            ->label('Generated')
                            ->color('success'),
                        
                        Infolists\Components\TextEntry::make('published_count')
                            ->label('Published')
                            ->color('info'),
                        
                        Infolists\Components\TextEntry::make('exported_count')
                            ->label('Exported')
                            ->color('warning'),
                        
                        Infolists\Components\TextEntry::make('failed_count')
                            ->label('Failed')
                            ->color('danger'),
                    ])
                    ->columns(5),

                Infolists\Components\Section::make('Timing')
                    ->schema([
                        Infolists\Components\TextEntry::make('started_at')
                            ->dateTime(),
                        
                        Infolists\Components\TextEntry::make('completed_at')
                            ->dateTime(),
                        
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                    ])
                    ->columns(3),
            ]);
    }
}
