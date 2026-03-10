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
                    ->query(fn (Builder $query): Builder => $query
                        ->leftJoin('internal_links', function ($join) {
                            $join->on('pages.url', '=', 'internal_links.target_url')
                                 ->on('pages.site_id', '=', 'internal_links.site_id');
                        })
                        ->whereNull('internal_links.id')
                        ->select('pages.*')
                    )
                    ->toggle(),

                Filter::make('weak_links')
                    ->label('Weak Links (< 2 incoming)')
                    ->query(function (Builder $query): Builder {
                        return $query
                            ->leftJoin('internal_links', function ($join) {
                                $join->on('pages.url', '=', 'internal_links.target_url')
                                     ->on('pages.site_id', '=', 'internal_links.site_id');
                            })
                            ->select('pages.*')
                            ->groupBy('pages.id', 'pages.site_id', 'pages.url', 'pages.path', 'pages.title', 'pages.status_code', 'pages.crawl_status', 'pages.last_crawled_at', 'pages.created_at', 'pages.updated_at')
                            ->havingRaw('COUNT(internal_links.id) < 2')
                            ->havingRaw('COUNT(internal_links.id) > 0');
                    })
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
