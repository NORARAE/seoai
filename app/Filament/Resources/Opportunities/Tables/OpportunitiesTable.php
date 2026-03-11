<?php

namespace App\Filament\Resources\Opportunities\Tables;

use App\Filament\Resources\Pages\PageResource;
use App\Services\TitleSuggestionService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
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
                Action::make('view_page')
                    ->label('View Page')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record) => PageResource::getUrl('view', ['record' => $record->page_id]))
                    ->openUrlInNewTab(false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('generate_title_suggestions')
                        ->label('Generate Title Suggestions')
                        ->icon('heroicon-o-sparkles')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Generate Title Suggestions')
                        ->modalDescription('Generate title suggestions for pages related to selected missing_title opportunities.')
                        ->action(function ($records) {
                            $service = app(TitleSuggestionService::class);
                            $generated = 0;
                            $skipped = 0;
                            
                            foreach ($records as $opportunity) {
                                // Only process missing_title opportunities
                                if ($opportunity->issue_type === 'missing_title' && $opportunity->page) {
                                    $page = $opportunity->page;
                                    
                                    // Only generate if page doesn't have a title
                                    if (empty($page->title)) {
                                        $suggestion = $service->generateSuggestion($page);
                                        $page->update(['suggested_title' => $suggestion]);
                                        $generated++;
                                    } else {
                                        $skipped++;
                                    }
                                } else {
                                    $skipped++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Title Suggestions Generated')
                                ->body("{$generated} suggestions generated. {$skipped} items skipped.")
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('mark_resolved')
                        ->label('Mark Resolved')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Mark Opportunities as Resolved')
                        ->modalDescription('Mark selected opportunities as resolved.')
                        ->action(function ($records) {
                            $count = 0;
                            
                            foreach ($records as $opportunity) {
                                if ($opportunity->status !== 'resolved') {
                                    $opportunity->update(['status' => 'resolved']);
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Opportunities Resolved')
                                ->body("{$count} opportunities marked as resolved.")
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('ignore')
                        ->label('Ignore')
                        ->icon('heroicon-o-x-circle')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Ignore Opportunities')
                        ->modalDescription('Mark selected opportunities as ignored.')
                        ->action(function ($records) {
                            $count = 0;
                            
                            foreach ($records as $opportunity) {
                                if ($opportunity->status !== 'ignored') {
                                    $opportunity->update(['status' => 'ignored']);
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Opportunities Ignored')
                                ->body("{$count} opportunities marked as ignored.")
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('priority_score', 'desc');
    }
}
