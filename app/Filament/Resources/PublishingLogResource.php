<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\FrontendDevRestricted;

use App\Filament\Resources\PublishingLogResource\Pages;
use App\Models\PublishingLog;
use App\Models\User;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PublishingLogResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = PublishingLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 8;

    protected static ?string $label = 'Publishing Log';

    public static function canCreate(): bool
    {
        return false; // Logs are created automatically, not manually
    }

    public static function canViewAny(): bool
    {
        return Auth::user() instanceof User;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if (!$user instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isSuperAdmin()) {
            return $query;
        }

        $siteIds = $user->accessibleSites()->pluck('sites.id');

        if ($siteIds->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('payload', fn(Builder $payloadQuery) => $payloadQuery->whereIn('site_id', $siteIds));
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
                        'danger' => 'failure',
                    ]),

                Tables\Columns\TextColumn::make('remote_url')
                    ->limit(40)
                    ->url(fn($record) => $record->remote_url, shouldOpenInNewTab: true)
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
                        'failure' => 'Failed',
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
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                ViewAction::make(),
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
