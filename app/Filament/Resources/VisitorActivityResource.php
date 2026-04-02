<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\FrontendDevRestricted;
use App\Filament\Resources\VisitorActivityResource\Pages\ListVisitorActivities;
use App\Models\UserSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VisitorActivityResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = UserSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEye;

    protected static ?string $navigationLabel = 'Visitor Activity';

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 10;

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
            ->defaultSort('last_activity_at', 'desc')
            ->recordClasses(
                fn(UserSession $r): string => ($r->visited_onboarding || $r->opened_booking_modal)
                ? 'bg-warning-50 dark:bg-warning-950/30'
                : ''
            )
            ->columns([
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),

                TextColumn::make('country')
                    ->label('Country')
                    ->default('—')
                    ->sortable(),

                TextColumn::make('city')
                    ->label('City')
                    ->default('—'),

                TextColumn::make('first_page')
                    ->label('First Page')
                    ->limit(50)
                    ->tooltip(fn(UserSession $r): string => $r->first_page ?? ''),

                TextColumn::make('last_page')
                    ->label('Last Page')
                    ->limit(50)
                    ->tooltip(fn(UserSession $r): string => $r->last_page ?? ''),

                IconColumn::make('visited_book')
                    ->label('Book')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCalendar)
                    ->falseIcon(Heroicon::OutlinedMinus)
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('first_book_at')
                    ->label('First: Book')
                    ->since()
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('opened_booking_modal')
                    ->label('Modal')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCursorArrowRays)
                    ->falseIcon(Heroicon::OutlinedMinus)
                    ->trueColor('warning')
                    ->falseColor('gray'),

                TextColumn::make('first_modal_open_at')
                    ->label('First: Modal')
                    ->since()
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('visited_onboarding')
                    ->label('Onboarding')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedRocketLaunch)
                    ->falseIcon(Heroicon::OutlinedMinus)
                    ->trueColor('info')
                    ->falseColor('gray'),

                TextColumn::make('first_onboarding_at')
                    ->label('First: Onboarding')
                    ->since()
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('last_activity_at')
                    ->label('Last Active')
                    ->since()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('First Seen')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(40)
                    ->tooltip(fn(UserSession $r): string => $r->user_agent ?? '')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('high_intent')
                    ->label('High intent (modal or onboarding)')
                    ->query(fn(Builder $q): Builder => $q->where(function (Builder $inner): void {
                        $inner->where('opened_booking_modal', true)
                            ->orWhere('visited_onboarding', true);
                    })),

                Filter::make('reached_book')
                    ->label('Reached Book Page')
                    ->query(fn(Builder $q): Builder => $q->where('visited_book', true)),

                Filter::make('opened_modal')
                    ->label('Opened Modal')
                    ->query(fn(Builder $q): Builder => $q->where('opened_booking_modal', true)),

                Filter::make('reached_onboarding')
                    ->label('Reached Onboarding')
                    ->query(fn(Builder $q): Builder => $q->where('visited_onboarding', true)),

                Filter::make('has_location')
                    ->label('Location known')
                    ->query(fn(Builder $q): Builder => $q->whereNotNull('country')),
            ])
            ->emptyStateHeading('No visitor sessions recorded yet')
            ->emptyStateDescription('Sessions are tracked automatically when pages are visited.')
            ->striped()
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVisitorActivities::route('/'),
        ];
    }
}
