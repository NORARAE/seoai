<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use App\Mail\BookingConfirmed;
use App\Mail\BookingFollowUp;
use App\Mail\BookingPreCall;
use App\Models\Booking;
use App\Models\EmailLog;
use App\Models\OnboardingSubmission;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Mail;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resend_confirmation')
                ->label('Resend Confirmation')
                ->icon('heroicon-o-envelope')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Resend confirmation email?')
                ->modalDescription('A new confirmation email will be sent to the client immediately.')
                ->action(function () {
                    try {
                        Mail::to($this->record->email)->queue(new BookingConfirmed($this->record));
                        EmailLog::create([
                            'booking_id' => $this->record->id,
                            'email_type' => 'confirmation',
                            'recipient_email' => $this->record->email,
                            'sent_at' => now(),
                            'status' => 'sent',
                        ]);
                        Notification::make()->title('Confirmation resent')->success()->send();
                    } catch (\Exception $e) {
                        EmailLog::create([
                            'booking_id' => $this->record->id,
                            'email_type' => 'confirmation',
                            'recipient_email' => $this->record->email,
                            'status' => 'failed',
                            'error_message' => $e->getMessage(),
                        ]);
                        Notification::make()->title('Email failed: ' . $e->getMessage())->danger()->send();
                    }
                }),

            Action::make('send_pre_call')
                ->label('Send Pre-Call Now')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Send pre-call email now?')
                ->modalDescription('The pre-call primer will be sent to the client immediately, regardless of session timing.')
                ->action(function () {
                    try {
                        Mail::to($this->record->email)->queue(new BookingPreCall($this->record));
                        EmailLog::create([
                            'booking_id' => $this->record->id,
                            'email_type' => 'pre_call',
                            'recipient_email' => $this->record->email,
                            'sent_at' => now(),
                            'status' => 'sent',
                        ]);
                        Notification::make()->title('Pre-call email sent')->success()->send();
                    } catch (\Exception $e) {
                        EmailLog::create([
                            'booking_id' => $this->record->id,
                            'email_type' => 'pre_call',
                            'recipient_email' => $this->record->email,
                            'status' => 'failed',
                            'error_message' => $e->getMessage(),
                        ]);
                        Notification::make()->title('Email failed: ' . $e->getMessage())->danger()->send();
                    }
                }),

            Action::make('send_follow_up')
                ->label('Send Follow-Up')
                ->icon('heroicon-o-arrow-uturn-right')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Send follow-up email?')
                ->modalDescription('A post-session follow-up email will be sent to the client.')
                ->action(function () {
                    try {
                        Mail::to($this->record->email)->queue(new BookingFollowUp($this->record));
                        EmailLog::create([
                            'booking_id' => $this->record->id,
                            'email_type' => 'follow_up',
                            'recipient_email' => $this->record->email,
                            'sent_at' => now(),
                            'status' => 'sent',
                        ]);
                        Notification::make()->title('Follow-up email sent')->success()->send();
                    } catch (\Exception $e) {
                        EmailLog::create([
                            'booking_id' => $this->record->id,
                            'email_type' => 'follow_up',
                            'recipient_email' => $this->record->email,
                            'status' => 'failed',
                            'error_message' => $e->getMessage(),
                        ]);
                        Notification::make()->title('Email failed: ' . $e->getMessage())->danger()->send();
                    }
                }),

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
                TextEntry::make('booking_type')
                    ->label('Booking Type')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'consultation' => 'warning',
                        'activation' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'consultation' => 'AI Visibility Consultation',
                        'activation' => 'Full System Activation',
                        default => 'Free Discovery Call',
                    }),
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

            Section::make('Email Activity')->schema([
                TextEntry::make('email_confirmation_status')
                    ->label('Confirmation')
                    ->state(function (Booking $record): string {
                        $log = $record->emailLogs()->where('email_type', 'confirmation')->latest()->first();
                        if (!$log)
                            return 'Not sent';
                        return ucfirst($log->status) . ($log->sent_at ? ' · ' . $log->sent_at->format('M j, Y g:i A') : '');
                    })
                    ->badge()
                    ->color(fn(string $state): string => str_starts_with($state, 'Sent') ? 'success' : (str_starts_with($state, 'Failed') ? 'danger' : 'gray')),

                TextEntry::make('email_pre_call_status')
                    ->label('Pre-Call')
                    ->state(function (Booking $record): string {
                        $log = $record->emailLogs()->where('email_type', 'pre_call')->latest()->first();
                        if (!$log)
                            return 'Not sent';
                        return ucfirst($log->status) . ($log->sent_at ? ' · ' . $log->sent_at->format('M j, Y g:i A') : '');
                    })
                    ->badge()
                    ->color(fn(string $state): string => str_starts_with($state, 'Sent') ? 'success' : (str_starts_with($state, 'Scheduled') ? 'warning' : (str_starts_with($state, 'Failed') ? 'danger' : 'gray'))),

                TextEntry::make('email_follow_up_status')
                    ->label('Follow-Up')
                    ->state(function (Booking $record): string {
                        $log = $record->emailLogs()->where('email_type', 'follow_up')->latest()->first();
                        if (!$log)
                            return 'Not sent';
                        return ucfirst($log->status) . ($log->sent_at ? ' · ' . $log->sent_at->format('M j, Y g:i A') : '');
                    })
                    ->badge()
                    ->color(fn(string $state): string => str_starts_with($state, 'Sent') ? 'success' : (str_starts_with($state, 'Failed') ? 'danger' : 'gray')),
            ])->columns(3),

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

            Section::make('Onboarding Status')->schema([
                TextEntry::make('onboarding_submitted')
                    ->label('Onboarding Submitted')
                    ->getStateUsing(function (Booking $record): string {
                        return $record->lead?->onboarding_status === 'submitted' ? 'Yes' : 'No';
                    })
                    ->badge()
                    ->color(fn(string $state) => $state === 'Yes' ? 'success' : 'warning'),
                TextEntry::make('onboarding_submitted_at')
                    ->label('Submitted At')
                    ->getStateUsing(function (Booking $record): ?string {
                        $lead = $record->lead;
                        if (!$lead)
                            return null;
                        $sub = OnboardingSubmission::where('lead_id', $lead->id)
                            ->orderByDesc('submitted_at')
                            ->first();
                        return $sub?->submitted_at?->format('M j, Y g:i A');
                    })
                    ->placeholder('Not yet submitted'),
                TextEntry::make('lead_stage')
                    ->label('Lead Stage')
                    ->getStateUsing(fn(Booking $record): string => $record->lead?->lifecycle_stage ?? '—'),
                TextEntry::make('lead_tags')
                    ->label('Tags')
                    ->getStateUsing(function (Booking $record): string {
                        $tags = $record->lead?->tags ?? [];
                        return !empty($tags) ? implode(', ', $tags) : '—';
                    }),
                TextEntry::make('onboarding_goals')
                    ->label('Goals')
                    ->columnSpanFull()
                    ->getStateUsing(function (Booking $record): ?string {
                        $lead = $record->lead;
                        if (!$lead)
                            return null;
                        return OnboardingSubmission::where('lead_id', $lead->id)
                            ->orderByDesc('submitted_at')
                            ->value('goals');
                    })
                    ->placeholder('—'),
            ])->columns(2),
        ]);
    }
}

