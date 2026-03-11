<?php

namespace App\Filament\Resources\LocationPages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LocationPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'county_hub' => 'info',
                        'service_city' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'county_hub' => 'County Hub',
                        'service_city' => 'Service-City',
                        default => $state,
                    })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'warning',
                        'published' => 'success',
                        'archived' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(50)
                    ->weight(FontWeight::Medium)
                    ->tooltip(fn ($record) => $record->title),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->limit(40)
                    ->copyable()
                    ->tooltip(fn ($record) => $record->slug)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('url_path')
                    ->label('URL Path')
                    ->searchable()
                    ->limit(40)
                    ->copyable()
                    ->url(fn ($record) => $record->canonical_url, shouldOpenInNewTab: true)
                    ->tooltip(fn ($record) => $record->url_path),

                TextColumn::make('score')
                    ->label('Score')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === null => 'gray',
                        $state >= 70 => 'success',
                        $state >= 50 => 'info',
                        default => 'warning',
                    })
                    ->placeholder('—'),

                TextColumn::make('county.name')
                    ->label('County')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('city.name')
                    ->label('City')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),

                IconColumn::make('is_indexable')
                    ->label('Indexable')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('generated_at')
                    ->label('Generated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Page Type')
                    ->options([
                        'county_hub' => 'County Hub',
                        'service_city' => 'Service-City',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ]),

                SelectFilter::make('county_id')
                    ->label('County')
                    ->relationship('county', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('service_id')
                    ->label('Service')
                    ->relationship('service', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('score')
                    ->label('High Score (70+)')
                    ->query(fn (Builder $query): Builder => $query->where('score', '>=', 70))
                    ->toggle(),

                Filter::make('qualified')
                    ->label('Qualified (50+)')
                    ->query(fn (Builder $query): Builder => $query->where('score', '>=', 50))
                    ->toggle(),

                Filter::make('is_indexable')
                    ->label('Indexable Only')
                    ->query(fn (Builder $query): Builder => $query->where('is_indexable', true))
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort(fn (Builder $query) => $query
                ->orderByDesc('score')
                ->orderByDesc('generated_at')
            );
    }
}
