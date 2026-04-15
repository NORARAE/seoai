<?php

namespace App\Filament\Resources\Bookings;

use App\Filament\Concerns\FrontendDevRestricted;
use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Filament\Resources\Bookings\Pages\ViewBooking;
use App\Models\Booking;
use App\Models\ConsultType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class BookingResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = Booking::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $navigationLabel = 'Bookings';

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 3;

    // Navigation handled by manual NavigationItem in AdminPanelProvider
    // to avoid route name conflict with AdminBookingController routes.
    protected static bool $shouldRegisterNavigation = false;

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('preferred_date', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('consultType.name')
                    ->label('Session Type')
                    ->sortable(),

                TextColumn::make('booking_type')
                    ->label('Type')
                    ->badge()
                    ->sortable()
                    ->color(fn(?string $state): string => match ($state) {
                        'audit' => 'warning',
                        'strategy' => 'info',
                        'build' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'audit' => 'Audit $500',
                        'strategy' => 'Strategy',
                        'build' => 'Build',
                        default => 'Discovery',
                    }),

                TextColumn::make('preferred_date')
                    ->label('Date')
                    ->date('M j, Y')
                    ->sortable(),

                TextColumn::make('preferred_time')
                    ->label('Time')
                    ->formatStateUsing(fn(string $state) => \Carbon\Carbon::parse($state)->format('g:i A') . ' PT'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->color(fn(string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'pending' => 'warning',
                        'awaiting_payment' => 'info',
                        'cancelled' => 'danger',
                        'no_show' => 'danger',
                        'completed' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('company')
                    ->label('Company')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('reschedule_count')
                    ->label('Rescheduled')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('email_confirmation')
                    ->label('✉ Confirmation')
                    ->state(
                        fn(Booking $record): string =>
                        $record->emailLogs()->where('email_type', 'confirmation')->where('status', 'sent')->latest()->value('sent_at')
                        ? \Carbon\Carbon::parse($record->emailLogs()->where('email_type', 'confirmation')->where('status', 'sent')->latest()->value('sent_at'))->format('M j g:i A')
                        : ($record->emailLogs()->where('email_type', 'confirmation')->where('status', 'failed')->exists() ? 'Failed' : '—')
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('email_pre_call')
                    ->label('✉ Pre-Call')
                    ->state(
                        fn(Booking $record): string =>
                        $record->emailLogs()->where('email_type', 'pre_call')->where('status', 'scheduled')->latest()->value('sent_at')
                        ? 'Sched ' . \Carbon\Carbon::parse($record->emailLogs()->where('email_type', 'pre_call')->where('status', 'scheduled')->latest()->value('sent_at'))->format('M j g:i A')
                        : ($record->emailLogs()->where('email_type', 'pre_call')->where('status', 'sent')->exists() ? 'Sent' : '—')
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('email_follow_up')
                    ->label('✉ Follow-Up')
                    ->state(
                        fn(Booking $record): string =>
                        $record->emailLogs()->where('email_type', 'follow_up')->where('status', 'sent')->exists() ? 'Sent' : '—'
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('booking_type')
                    ->label('Booking Type')
                    ->options([
                        'discovery' => 'Discovery (Free)',
                        'audit' => 'Audit ($500)',
                        'strategy' => 'Strategy',
                        'build' => 'Build',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'awaiting_payment' => 'Awaiting Payment',
                        'cancelled' => 'Cancelled',
                        'no_show' => 'No Show',
                        'completed' => 'Completed',
                    ]),

                SelectFilter::make('consult_type_id')
                    ->label('Session Type')
                    ->options(fn() => ConsultType::orderBy('sort_order')->pluck('name', 'id')),
            ])
            ->actions([
                Action::make('mark_no_show')
                    ->label('No Show')
                    ->icon('heroicon-o-user-minus')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Mark as No Show')
                    ->modalDescription('This client did not attend. The booking will be marked as no_show.')
                    ->visible(fn(Booking $record) => $record->status === 'confirmed')
                    ->action(function (Booking $record) {
                        $record->update(['status' => 'no_show']);
                    }),
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(Booking $record) => !in_array($record->status, ['cancelled', 'completed', 'no_show']))
                    ->action(function (Booking $record) {
                        $record->update([
                            'status' => 'cancelled',
                            'cancelled_at' => now(),
                        ]);
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookings::route('/'),
            'view' => ViewBooking::route('/{record}'),
        ];
    }
}
