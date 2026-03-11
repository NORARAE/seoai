<?php

namespace App\Filament\Resources\Pages\Tables;

use App\Filament\Resources\Pages\PageFilters;
use App\Services\LinkSuggestionService;
use App\Services\TitleSuggestionService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
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

                TextColumn::make('suggested_title')
                    ->label('Suggested Title')
                    ->searchable()
                    ->limit(40)
                    ->placeholder('—')
                    ->color('info')
                    ->icon('heroicon-o-light-bulb')
                    ->tooltip(fn ($record) => $record->suggested_title)
                    ->toggleable(isToggledHiddenByDefault: true),

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

                TextColumn::make('link_suggestions_count')
                    ->label('Link Ideas')
                    ->counts('linkSuggestions')
                    ->sortable()
                    ->alignEnd()
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

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

                Filter::make(PageFilters::MISSING_TITLE)
                    ->label('Missing Title')
                    ->query(fn (Builder $query): Builder => $query->missingTitle())
                    ->toggle(),

                Filter::make(PageFilters::BROKEN)
                    ->label('Broken Pages (4xx/5xx)')
                    ->query(fn (Builder $query): Builder => $query->broken())
                    ->toggle(),

                Filter::make(PageFilters::DISCOVERED)
                    ->label('Awaiting Crawl')
                    ->query(fn (Builder $query): Builder => $query->discovered())
                    ->toggle(),

                Filter::make(PageFilters::ORPHAN)
                    ->label('Orphan Pages (0 incoming links)')
                    ->query(fn (Builder $query): Builder => $query->where('incoming_links_count', 0))
                    ->toggle(),

                Filter::make(PageFilters::WEAK_LINKS)
                    ->label('Weak Links (< 2 incoming)')
                    ->query(fn (Builder $query): Builder => $query
                        ->where('incoming_links_count', '>', 0)
                        ->where('incoming_links_count', '<', 2)
                    )
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('generate_title')
                    ->label('Suggest Title')
                    ->icon('heroicon-o-sparkles')
                    ->color('info')
                    ->visible(fn ($record) => empty($record->title))
                    ->requiresConfirmation()
                    ->modalHeading('Generate Title Suggestion')
                    ->modalDescription(fn ($record) => 'Generate an SEO-friendly title suggestion for: ' . $record->url)
                    ->action(function ($record) {
                        $service = app(TitleSuggestionService::class);
                        $suggestion = $service->generateSuggestion($record);
                        
                        $record->update(['suggested_title' => $suggestion]);
                        
                        Notification::make()
                            ->title('Title Suggested')
                            ->body('Suggested title: ' . $suggestion)
                            ->success()
                            ->send();
                    }),
                Action::make('generate_links')
                    ->label('Suggest Links')
                    ->icon('heroicon-o-link')
                    ->color('warning')
                    ->visible(fn ($record) => $record->incoming_links_count < 2)
                    ->requiresConfirmation()
                    ->modalHeading('Generate Link Suggestions')
                    ->modalDescription(fn ($record) => 'Find pages that could link to: ' . $record->url)
                    ->action(function ($record) {
                        $service = app(LinkSuggestionService::class);
                        $suggestions = $service->generateSuggestionsForPage($record);
                        
                        $count = $suggestions->count();
                        
                        if ($count > 0) {
                            Notification::make()
                                ->title('Link Suggestions Generated')
                                ->body("Found {$count} potential link opportunities. Toggle 'Link Ideas' column to see count.")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('No Suggestions Found')
                                ->body('Could not find suitable pages to link from.')
                                ->warning()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('generate_title_suggestions')
                        ->label('Generate Title Suggestions')
                        ->icon('heroicon-o-sparkles')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Generate Title Suggestions')
                        ->modalDescription('Generate SEO-friendly title suggestions for selected pages that are missing titles.')
                        ->action(function ($records) {
                            $service = app(TitleSuggestionService::class);
                            $generated = 0;
                            $skipped = 0;
                            
                            foreach ($records as $record) {
                                // Only generate for pages with empty title
                                if (empty($record->title)) {
                                    $suggestion = $service->generateSuggestion($record);
                                    $record->update(['suggested_title' => $suggestion]);
                                    $generated++;
                                } else {
                                    $skipped++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Title Suggestions Generated')
                                ->body("{$generated} suggestions generated. {$skipped} pages skipped (already have titles).")
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('apply_suggested_titles')
                        ->label('Apply Suggested Titles')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Apply Suggested Titles')
                        ->modalDescription('Copy suggested titles to the title field for selected pages. This will update the page titles.')
                        ->action(function ($records) {
                            $applied = 0;
                            $skipped = 0;
                            
                            foreach ($records as $record) {
                                if (!empty($record->suggested_title)) {
                                    $record->update(['title' => $record->suggested_title]);
                                    $applied++;
                                    
                                    // Update related missing_title opportunities to resolved
                                    $record->opportunities()
                                        ->where('issue_type', 'missing_title')
                                        ->where('status', 'open')
                                        ->update(['status' => 'resolved']);
                                } else {
                                    $skipped++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Titles Applied')
                                ->body("{$applied} titles applied. {$skipped} pages skipped (no suggestions available).")
                                ->success()
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
