<?php

namespace App\Filament\Resources\LinkSuggestions\Tables;

use App\Filament\Resources\Pages\PageResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LinkSuggestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sourcePage.url')
                    ->label('Source Page')
                    ->searchable()
                    ->limit(40)
                    ->url(fn ($record) => PageResource::getUrl('view', ['record' => $record->source_page_id]))
                    ->tooltip(fn ($record) => $record->sourcePage->url),

                TextColumn::make('targetPage.url')
                    ->label('Target Page')
                    ->searchable()
                    ->limit(40)
                    ->url(fn ($record) => PageResource::getUrl('view', ['record' => $record->target_page_id]))
                    ->tooltip(fn ($record) => $record->targetPage->url),

                TextColumn::make('suggested_anchor_text')
                    ->label('Anchor Text')
                    ->limit(30)
                    ->searchable()
                    ->tooltip(fn ($record) => $record->suggested_anchor_text),

                TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->reason),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
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

                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('accept')
                    ->label('Accept')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Accept Link Suggestion')
                    ->modalDescription(fn ($record) => "Accept adding '{$record->suggested_anchor_text}' link from source to target page?")
                    ->action(function ($record) {
                        $record->update(['status' => 'accepted']);
                        Notification::make()
                            ->title('Suggestion Accepted')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'rejected']);
                        Notification::make()
                            ->title('Suggestion Rejected')
                            ->body('Marked as rejected')
                            ->send();
                    }),
                Action::make('open_source')
                    ->label('Open Source')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record) => $record->sourcePage->url)
                    ->openUrlInNewTab(),
                Action::make('open_target')
                    ->label('Open Target')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record) => $record->targetPage->url)
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc')
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
