<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PublishingLogResource\Pages;
use App\Models\PublishingLog;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PublishingLogResource extends Resource
{
    protected static ?string $model = PublishingLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 3;

    protected static ?string $label = 'Publishing Log';

    public static function canCreate(): bool
    {
        return false; // Logs are created automatically, not manually
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Log Information')
                    ->schema([
                        Forms\Components\Select::make('payload_id')
                            ->relationship('payload', 'title')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('adapter_type')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('action')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('result')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\Textarea::make('remote_url')
                            ->disabled()
                            ->rows(2),
                        
                        Forms\Components\Textarea::make('error_message')
                            ->disabled()
                            ->rows(3),
                        
                        Forms\Components\Textarea::make('remote_response')
                            ->disabled()
                            ->rows(10)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Timing')
                    ->schema([
                        Forms\Components\DateTimePicker::make('created_at')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('payload.title')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('payload.site.name')
                    ->label('Site')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('adapter_type')
                    ->label('Adapter')
                    ->colors([
                        'primary' => 'wordpress',
                        'warning' => 'export',
                    ]),
                
                Tables\Columns\BadgeColumn::make('action')
                    ->colors([
                        'success' => 'publish',
                        'warning' => 'export',
                        'info' => 'update',
                        'danger' => 'delete',
                    ]),
                
                Tables\Columns\BadgeColumn::make('result')
                    ->colors([
                        'success' => 'success',
                        'danger' => 'failed',
                    ]),
                
                Tables\Columns\TextColumn::make('remote_url')
                    ->limit(40)
                    ->url(fn ($record) => $record->remote_url, shouldOpenInNewTab: true)
                    ->placeholder('N/A'),
                
                Tables\Columns\TextColumn::make('error_message')
                    ->limit(40)
                    ->searchable()
                    ->placeholder('—')
                    ->color('danger'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('result')
                    ->options([
                        'success' => 'Success',
                        'failed' => 'Failed',
                    ]),
                
                Tables\Filters\SelectFilter::make('adapter_type')
                    ->label('Adapter')
                    ->options([
                        'wordpress' => 'WordPress',
                        'export' => 'Export',
                    ]),
                
                Tables\Filters\SelectFilter::make('action')
                    ->options([
                        'publish' => 'Publish',
                        'export' => 'Export',
                        'update' => 'Update',
                        'delete' => 'Delete',
                    ]),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPublishingLogs::route('/'),
            'view' => Pages\ViewPublishingLog::route('/{record}'),
        ];
    }
}
