<?php

namespace App\Filament\Resources\Pages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('site.domain')
                    ->label('Site')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold),

                TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->limit(50)
                    ->copyable()
                    ->url(fn ($record) => $record->url, shouldOpenInNewTab: true)
                    ->tooltip(fn ($record) => $record->url),

                TextColumn::make('path')
                    ->searchable()
                    ->limit(30)
                    ->placeholder('/')
                    ->tooltip(fn ($record) => $record->path),

                TextColumn::make('title')
                    ->searchable()
                    ->limit(40)
                    ->placeholder('—')
                    ->tooltip(fn ($record) => $record->title),

                TextColumn::make('status_code')
                    ->label('HTTP')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 200 && $state < 300 => 'success',
                        $state >= 300 && $state < 400 => 'info',
                        $state >= 400 && $state < 500 => 'warning',
                        $state >= 500 => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('crawl_status')
                    ->label('Crawl Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'discovered' => 'gray',
                        'crawling' => 'info',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('incoming_links_count')
                    ->label('In Links')
                    ->numeric()
                    ->sortable()
                    ->alignEnd()
                    ->color(fn ($state) => $state === 0 ? 'danger' : ($state < 2 ? 'warning' : 'success')),

                TextColumn::make('outgoing_links_count')
                    ->label('Out Links')
                    ->numeric()
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('last_crawled_at')
                    ->label('Last Crawled')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Discovered')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('site_id')
                    ->label('Site')
                    ->relationship('site', 'domain')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('crawl_status')
                    ->options([
                        'discovered' => 'Discovered',
                        'crawling' => 'Crawling',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),

                Filter::make('missing_title')
                    ->label('Missing Title')
                    ->query(fn (Builder $query): Builder => $query->missingTitle())
                    ->toggle(),

                Filter::make('broken')
                    ->label('Broken Pages (4xx/5xx)')
                    ->query(fn (Builder $query): Builder => $query->broken())
                    ->toggle(),

                Filter::make('discovered')
                    ->label('Awaiting Crawl')
                    ->query(fn (Builder $query): Builder => $query->discovered())
                    ->toggle(),

                Filter::make('orphan')
                    ->label('Orphan Pages (0 incoming links)')
                    ->query(fn (Builder $query): Builder => $query->where('incoming_links_count', 0))
                    ->toggle(),

                Filter::make('weak_links')
                    ->label('Weak Links (< 2 incoming)')
                    ->query(fn (Builder $query): Builder => $query
                        ->where('incoming_links_count', '>', 0)
                        ->where('incoming_links_count', '<', 2)
                    )
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
