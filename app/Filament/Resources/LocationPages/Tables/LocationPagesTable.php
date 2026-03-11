<?php

namespace App\Filament\Resources\LocationPages\Tables;

use Filament\Actions\BulkAction;
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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

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

                TextColumn::make('content_quality_status')
                    ->label('Quality')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unreviewed' => 'gray',
                        'edited' => 'warning',
                        'approved' => 'success',
                        'excluded' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                IconColumn::make('needs_review')
                    ->label('Needs Review')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

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

                SelectFilter::make('content_quality_status')
                    ->label('Quality Status')
                    ->options([
                        'unreviewed' => 'Unreviewed',
                        'edited' => 'Edited',
                        'approved' => 'Approved',
                        'excluded' => 'Excluded',
                    ]),

                Filter::make('needs_review')
                    ->label('Needs Review')
                    ->query(fn (Builder $query): Builder => $query->where('needs_review', true))
                    ->toggle(),

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
                    BulkAction::make('markApproved')
                        ->label('Mark as Approved')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'content_quality_status' => 'approved',
                                    'needs_review' => false,
                                    'approved_at' => now(),
                                    'approved_by' => Auth::id(),
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('markEdited')
                        ->label('Mark as Edited')
                        ->icon('heroicon-o-pencil')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'content_quality_status' => 'edited',
                                    'needs_review' => true,
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('markExcluded')
                        ->label('Mark as Excluded')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'content_quality_status' => 'excluded',
                                    'needs_review' => false,
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('markNeedsReview')
                        ->label('Mark Needs Review')
                        ->icon('heroicon-o-flag')
                        ->color('info')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'needs_review' => true,
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort(fn (Builder $query) => $query
                ->orderByDesc('score')
                ->orderByDesc('generated_at')
            );
    }
}
