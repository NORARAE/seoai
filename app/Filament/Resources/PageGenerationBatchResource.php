<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\FrontendDevRestricted;

use App\Filament\Resources\PageGenerationBatchResource\Pages;
use App\Models\PageGenerationBatch;
use App\Services\BulkPageExpansionService;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;

class PageGenerationBatchResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = PageGenerationBatch::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationLabel = 'Generation Batches';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 4;

    protected static ?string $label = 'Generation Batch';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('site.name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'pending',
                        'primary' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ]),

                Tables\Columns\TextColumn::make('requested_count')
                    ->label('Requested')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payload_count')
                    ->label('Generated')
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_count')
                    ->label('Published')
                    ->sortable(),

                Tables\Columns\TextColumn::make('failed_count')
                    ->label('Failed')
                    ->sortable()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('duration_seconds')
                    ->label('Duration')
                    ->formatStateUsing(fn(?int $state): string => $state ? gmdate('H:i:s', $state) : '—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),

                Tables\Filters\SelectFilter::make('site_id')
                    ->relationship('site', 'name')
                    ->label('Site'),
            ])
            ->actions([
                ViewAction::make(),

                Action::make('publish')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'completed' && $record->published_count < $record->payload_count)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $service = app(BulkPageExpansionService::class);
                        $dispatched = $service->publishBatch($record);

                        \Filament\Notifications\Notification::make()
                            ->title("{$dispatched} payloads queued for publishing")
                            ->success()
                            ->send();
                    }),

                Action::make('export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('format')
                            ->options([
                                'json' => 'JSON',
                                'markdown' => 'Markdown',
                                'html' => 'HTML',
                            ])
                            ->default('json')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $service = app(BulkPageExpansionService::class);
                        $exportPath = $service->exportBatch($record, $data['format']);

                        \Filament\Notifications\Notification::make()
                            ->title('Batch exported')
                            ->body("Download: {$exportPath}")
                            ->success()
                            ->send();
                    }),

                Action::make('cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record->status === 'processing')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $service = app(BulkPageExpansionService::class);
                        $service->cancelBatch($record);

                        \Filament\Notifications\Notification::make()
                            ->title('Batch cancelled')
                            ->warning()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPageGenerationBatches::route('/'),
            'create' => Pages\CreatePageGenerationBatch::route('/create'),
            'view' => Pages\ViewPageGenerationBatch::route('/{record}'),
        ];
    }
}
