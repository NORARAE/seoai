<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageGenerationBatchResource\Pages;
use App\Models\PageGenerationBatch;
use App\Services\BulkPageExpansionService;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class PageGenerationBatchResource extends Resource
{
    protected static ?string $model = PageGenerationBatch::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static ?int $navigationSort = 2;

    protected static ?string $label = 'Generation Batch';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Batch Information')
                    ->schema([
                        Forms\Components\Select::make('site_id')
                            ->relationship('site', 'name')
                            ->required(),
                        
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        
                        Forms\Components\Select::make('opportunity_source')
                            ->options([
                                'manual' => 'Manual',
                                'scan' => 'Automated Scan',
                                'scheduled' => 'Scheduled',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('requested_count')
                            ->numeric()
                            ->required()
                            ->default(20)
                            ->minValue(1)
                            ->maxValue(50),
                        
                        Forms\Components\Toggle::make('auto_publish')
                            ->label('Auto-publish after generation')
                            ->helperText('Only works for sites with native publishing mode (WordPress)'),
                    ]),

                Forms\Components\Section::make('Progress')
                    ->schema([
                        Forms\Components\TextInput::make('payload_count')
                            ->numeric()
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('published_count')
                            ->numeric()
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('exported_count')
                            ->numeric()
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('failed_count')
                            ->numeric()
                            ->disabled(),
                    ])
                    ->columns(4)
                    ->visibleOn('view'),
            ]);
    }

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
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
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
                Tables\Actions\ViewAction::make(),
                
                Action::make('publish')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'completed' && $record->published_count < $record->payload_count)
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
                    ->visible(fn ($record) => $record->status === 'processing')
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
