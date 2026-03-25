<?php

namespace App\Filament\Resources\ScanRunResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class FirstSeenUrlsRelationManager extends RelationManager
{
    protected static string $relationship = 'firstSeenUrls';

    protected static ?string $title = 'New URLs';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('normalized_url')
                    ->label('URL')
                    ->searchable()
                    ->limit(80),
                Tables\Columns\TextColumn::make('path')
                    ->limit(50),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'queued' => 'gray',
                        'processing' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('page_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('last_crawled_at')
                    ->dateTime()
                    ->placeholder('—'),
            ])
            ->emptyStateHeading('No newly discovered URLs in this scan run')
            ->emptyStateDescription('This run did not first discover any new URLs, or discoveries were already known from earlier runs.')
            ->defaultSort('id', 'asc');
    }
}
