<?php

namespace App\Filament\Resources\Opportunities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OpportunitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('priority_score')
                    ->label('Priority')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 40 => 'danger',
                        $state >= 20 => 'warning',
                        default => 'info',
                    })
                    ->weight(FontWeight::Bold),

                TextColumn::make('issue_type')
                    ->label('Issue')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),

                TextColumn::make('page.url')
                    ->label('Page')
                    ->searchable()
                    ->limit(50)
                    ->url(fn ($record) => $record->page->url, shouldOpenInNewTab: true)
                    ->tooltip(fn ($record) => $record->page->url),

                TextColumn::make('site.domain')
                    ->label('Site')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('recommendation')
                    ->label('Recommendation')
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->recommendation),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'warning',
                        'resolved' => 'success',
                        'ignored' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Detected')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('site_id')
                    ->label('Site')
                    ->relationship('site', 'domain')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('issue_type')
                    ->options([
                        'broken_page' => 'Broken Page',
                        'orphan_page' => 'Orphan Page',
                        'missing_title' => 'Missing Title',
                        'weak_internal_links' => 'Weak Internal Links',
                        'awaiting_crawl' => 'Awaiting Crawl',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'resolved' => 'Resolved',
                        'ignored' => 'Ignored',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('priority_score', 'desc');
    }
}
