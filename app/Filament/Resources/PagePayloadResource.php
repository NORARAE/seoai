<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PagePayloadResource\Pages;
use App\Models\PagePayload;
use App\Services\PublishingService;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class PagePayloadResource extends Resource
{
    protected static ?string $model = PagePayload::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Page Information')
                    ->schema([
                        Forms\Components\Select::make('site_id')
                            ->relationship('site', 'name')
                            ->required()
                            ->disabled(),
                        
                        Forms\Components\Select::make('service_id')
                            ->relationship('service', 'name')
                            ->disabled(),
                        
                        Forms\Components\Select::make('batch_id')
                            ->relationship('batch', 'name')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('meta_description')
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Content')
                    ->schema([
                        Forms\Components\RichEditor::make('body_content')
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('excerpt')
                            ->rows(2),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'ready' => 'Ready',
                                'published' => 'Published',
                                'archived' => 'Archived',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('publish_status')
                            ->options([
                                'pending' => 'Pending',
                                'published' => 'Published',
                                'exported' => 'Exported',
                                'failed' => 'Failed',
                            ])
                            ->required(),
                        
                        Forms\Components\TextInput::make('remote_id')
                            ->label('Remote ID')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('remote_url')
                            ->label('Remote URL')
                            ->url()
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->title),
                
                Tables\Columns\TextColumn::make('site.name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('service.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('batch.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'primary' => 'ready',
                        'success' => 'published',
                        'danger' => 'archived',
                    ]),
                
                Tables\Columns\BadgeColumn::make('publish_status')
                    ->label('Publish')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'published',
                        'info' => 'exported',
                        'danger' => 'failed',
                    ]),
                
                Tables\Columns\TextColumn::make('seo_score')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'ready' => 'Ready',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ]),
                
                Tables\Filters\SelectFilter::make('publish_status')
                    ->options([
                        'pending' => 'Pending',
                        'published' => 'Published',
                        'exported' => 'Exported',
                        'failed' => 'Failed',
                    ]),
                
                Tables\Filters\SelectFilter::make('site_id')
                    ->relationship('site', 'name')
                    ->label('Site'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Action::make('publish')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->color('success')
                    ->visible(fn ($record) => $record->publish_status === 'pending' && $record->status === 'ready')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        \App\Jobs\PublishPagePayloadJob::dispatch($record->id);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Publishing queued')
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
                                'csv' => 'CSV',
                            ])
                            ->default('json')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $exportContent = $record->toExportFormat($data['format']);
                        $filename = "payload-{$record->id}.{$data['format']}";
                        $path = "exports/single/{$filename}";
                        Storage::put($path, $exportContent);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Export ready')
                            ->body("Download: {$filename}")
                            ->success()
                            ->send();
                        
                        return response()->download(Storage::path($path));
                    }),
            ])
            ->bulkActions([
                BulkAction::make('export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('format')
                            ->options([
                                'json' => 'JSON',
                                'markdown' => 'Markdown',
                                'html' => 'HTML',
                                'csv' => 'CSV',
                            ])
                            ->default('json')
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data) {
                        $publishingService = app(PublishingService::class);
                        $zipPath = $publishingService->exportBatch($records, $data['format']);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Bulk export ready')
                            ->body("Download ZIP: {$zipPath}")
                            ->success()
                            ->send();
                    }),
                
                BulkAction::make('publish')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $queued = 0;
                        foreach ($records as $record) {
                            if ($record->isReadyToPublish()) {
                                \App\Jobs\PublishPagePayloadJob::dispatch($record->id);
                                $queued++;
                            }
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title("{$queued} payloads queued for publishing")
                            ->success()
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
            'index' => Pages\ListPagePayloads::route('/'),
            'view' => Pages\ViewPagePayload::route('/{record}'),
            'edit' => Pages\EditPagePayload::route('/{record}/edit'),
        ];
    }
}
