<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cancel')
                ->label('Cancel Booking')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cancel this booking?')
                ->modalDescription('This cannot be undone. The client will not be notified automatically.')
                ->visible(fn() => !in_array($this->record->status, ['cancelled', 'completed']))
                ->action(function () {
                    $this->record->update([
                        'status' => 'cancelled',
                        'cancelled_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Booking cancelled')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'cancelled_at']);
                }),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Booking Details')->schema([
                TextEntry::make('id')->label('Booking ID'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'pending' => 'warning',
                        'awaiting_payment' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextEntry::make('consultType.name')->label('Session Type'),
                TextEntry::make('preferred_date')->label('Date')->date('l, F j, Y'),
                TextEntry::make('preferred_time')
                    ->label('Time')
                    ->formatStateUsing(fn(string $state) => \Carbon\Carbon::parse($state)->format('g:i A') . ' PT'),
                TextEntry::make('confirmed_at')->label('Confirmed At')->dateTime()->placeholder('—'),
                TextEntry::make('cancelled_at')->label('Cancelled At')->dateTime()->placeholder('—'),
            ])->columns(2),

            Section::make('Client Information')->schema([
                TextEntry::make('name')->label('Name'),
                TextEntry::make('email')->label('Email'),
                TextEntry::make('phone')->label('Phone')->placeholder('—'),
                TextEntry::make('company')->label('Company')->placeholder('—'),
                TextEntry::make('website')->label('Website')->placeholder('—'),
                TextEntry::make('message')->label('Message')->placeholder('—')->columnSpanFull(),
            ])->columns(2),

            Section::make('Payment & Reminders')->schema([
                TextEntry::make('stripe_checkout_session_id')->label('Stripe Session')->placeholder('—'),
                TextEntry::make('stripe_payment_intent_id')->label('Payment Intent')->placeholder('—'),
                TextEntry::make('reminder_24h_sent_at')->label('24h Reminder Sent')->dateTime()->placeholder('Not sent'),
                TextEntry::make('reminder_2h_sent_at')->label('2h Reminder Sent')->dateTime()->placeholder('Not sent'),
                TextEntry::make('sms_opted_out')->label('SMS Opted Out')->badge()
                    ->formatStateUsing(fn(bool $state) => $state ? 'Yes' : 'No')
                    ->color(fn(bool $state) => $state ? 'danger' : 'success'),
                TextEntry::make('reschedule_count')->label('Reschedule Count'),
                TextEntry::make('last_rescheduled_at')->label('Last Rescheduled')->dateTime()->placeholder('—'),
            ])->columns(2),

            Section::make('Calendar & Token')->schema([
                TextEntry::make('google_event_id')->label('Calendar Event ID')->placeholder('—'),
                TextEntry::make('google_meet_link')->label('Meet Link')->placeholder('—'),
                TextEntry::make('public_booking_token')
                    ->label('Manage Token')
                    ->formatStateUsing(fn(?string $state) => $state
                        ? route('booking.manage', $state)
                        : '—')
                    ->copyable()
                    ->placeholder('—'),
            ])->columns(2),
        ]);
    }
}
