<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\FrontendDevRestricted;
use App\Filament\Resources\AiChatLogResource\Pages\ListAiChatLogs;
use App\Models\AiChatLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;

class AiChatLogResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = AiChatLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?string $navigationLabel = 'AI Chat Logs';

    protected static string|\UnitEnum|null $navigationGroup = 'Revenue';

    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && $user->canApproveUsers();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('domain')
                    ->label('Domain')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('user_message')
                    ->label('Message')
                    ->searchable()
                    ->limit(90)
                    ->tooltip(fn(AiChatLog $record): string => $record->user_message),

                TextColumn::make('intent')
                    ->label('Intent')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'education' => 'info',
                        'evaluation' => 'warning',
                        'action' => 'success',
                        'upgrade' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => $state ? ucfirst($state) : 'General')
                    ->sortable(),

                TextColumn::make('context_page')
                    ->label('Context')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'landing' => 'info',
                        'result' => 'success',
                        'dashboard' => 'warning',
                        'modal' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('intent')
                    ->options([
                        'education' => 'Education',
                        'evaluation' => 'Evaluation',
                        'action' => 'Action',
                        'upgrade' => 'Upgrade',
                        'general' => 'General',
                    ]),

                Filter::make('domain')
                    ->form([
                        TextInput::make('domain')->label('Domain contains'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['domain'] ?? null,
                            fn(Builder $q, string $domain): Builder => $q->where('domain', 'like', '%' . $domain . '%')
                        );
                    }),

                Filter::make('last_24h')
                    ->label('Recent: 24h')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subDay())),

                Filter::make('last_7d')
                    ->label('Recent: 7d')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7))),

                Filter::make('last_30d')
                    ->label('Recent: 30d')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(30))),
            ])
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAiChatLogs::route('/'),
        ];
    }
}
