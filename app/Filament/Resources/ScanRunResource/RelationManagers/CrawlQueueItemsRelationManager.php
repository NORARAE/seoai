<?php

namespace App\Filament\Resources\ScanRunResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CrawlQueueItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'crawlQueueItems';

    protected static ?string $title = 'Crawl Queue';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->searchable()
                    ->limit(80),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'queued' => 'gray',
                        'processing' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('depth')
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_attempted_at')
                    ->dateTime()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('error_message')
                    ->limit(60)
                    ->toggleable(),
            ])
            ->emptyStateHeading('No crawl queue items for this scan run')
            ->emptyStateDescription('This run has not queued any crawl work yet, or all related queue items were created outside this run scope.')
            ->defaultSort('id', 'asc');
    }
}
