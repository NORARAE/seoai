<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use App\Models\OnboardingSubmission;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    // ── Header actions ────────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            // Approve onboarding
            Action::make('approve')
                ->label('Approve Onboarding')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->onboarding_status === 'submitted')
                ->requiresConfirmation()
                ->modalHeading('Approve this onboarding?')
                ->modalDescription('The lead will be marked as approved. Use "Activate Client" once their account is set up.')
                ->action(function () {
                    $this->record->update([
                        'onboarding_status' => 'approved',
                        'lifecycle_stage'   => Lead::STAGE_APPROVED,
                    ]);
                    Notification::make()
                        ->title('Onboarding approved')
                        ->success()
                        ->send();
                }),

            // Activate client (approved → active)
            Action::make('activate')
                ->label('Activate Client')
                ->icon('heroicon-o-bolt')
                ->color('warning')
                ->visible(fn () => $this->record->onboarding_status === 'approved'
                    && $this->record->lifecycle_stage !== Lead::STAGE_ACTIVE)
                ->requiresConfirmation()
                ->modalHeading('Activate this client?')
                ->modalDescription('The lead will be marked as an active client. This should be done once their account / campaign is live.')
                ->action(function () {
                    $this->record->update([
                        'lifecycle_stage' => Lead::STAGE_ACTIVE,
                    ]);
                    Notification::make()
                        ->title('Client activated')
                        ->color('warning')
                        ->send();
                }),

            // Reject onboarding
            Action::make('reject')
                ->label('Reject Onboarding')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => in_array($this->record->onboarding_status, ['submitted', 'approved']))
                ->requiresConfirmation()
                ->modalHeading('Reject this onboarding?')
                ->modalDescription('The lead will be marked as rejected.')
                ->action(function () {
                    $this->record->update([
                        'onboarding_status' => 'rejected',
                        'lifecycle_stage'   => Lead::STAGE_REJECTED,
                    ]);
                    Notification::make()
                        ->title('Onboarding rejected')
                        ->danger()
                        ->send();
                }),

            // Download business license (auth-protected; links to signed admin route)
            Action::make('download_license')
                ->label('Download License')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn () => $this->record->onboardingSubmission?->hasLicense())
                ->url(fn () => route('onboarding.license.download', [
                    'submission' => $this->record->onboardingSubmission?->id,
                ]))
                ->openUrlInNewTab(),
        ];
    }

    // ── Infolist ──────────────────────────────────────────────────────────────

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Contact')
                ->columns(2)
                ->schema([
                    TextEntry::make('name')->label('Name'),
                    TextEntry::make('email')->label('Email'),
                    TextEntry::make('company')->label('Company')->placeholder('—'),
                    TextEntry::make('website')->label('Website')
                        ->url(fn ($state) => $state ?: null)
                        ->openUrlInNewTab()
                        ->placeholder('—'),
                    TextEntry::make('phone')->label('Phone')->placeholder('—'),
                    TextEntry::make('source')->label('Source')->badge()->color('gray'),
                ]),

            Section::make('Booking & Payment')
                ->columns(2)
                ->schema([
                    TextEntry::make('lifecycle_stage')
                        ->label('Pipeline Stage')
                        ->badge()
                        ->color(fn (?string $state): string => match ($state) {
                            Lead::STAGE_ACTIVE               => 'success',
                            Lead::STAGE_APPROVED             => 'success',
                            Lead::STAGE_ONBOARDING_SUBMITTED => 'info',
                            Lead::STAGE_PAID                 => 'warning',
                            Lead::STAGE_BOOKED               => 'gray',
                            Lead::STAGE_REJECTED             => 'danger',
                            Lead::STAGE_LOST                 => 'danger',
                            default                          => 'gray',
                        })
                        ->formatStateUsing(fn (?string $state) => match ($state) {
                            Lead::STAGE_ONBOARDING_SUBMITTED => 'Onboarding Submitted',
                            default                          => ucwords(str_replace('_', ' ', $state ?? 'new')),
                        }),
                    TextEntry::make('session_type')->label('Session Type')->placeholder('—'),
                    TextEntry::make('payment_status')->label('Payment')
                        ->badge()
                        ->color(fn (?string $state): string => match ($state) {
                            'paid'  => 'success',
                            'free'  => 'info',
                            default => 'gray',
                        })
                        ->placeholder('—'),
                    TextEntry::make('booking.preferred_date')
                        ->label('Booking Date')
                        ->date('F j, Y')
                        ->placeholder('—'),
                    TextEntry::make('booking.status')
                        ->label('Booking Status')
                        ->badge()
                        ->color(fn (?string $state): string => match ($state) {
                            'confirmed'        => 'success',
                            'awaiting_payment' => 'warning',
                            'cancelled'        => 'danger',
                            default            => 'gray',
                        })
                        ->placeholder('—'),
                ]),

            Section::make('Onboarding')
                ->columns(2)
                ->schema([
                    TextEntry::make('onboarding_status')
                        ->label('Status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'approved'  => 'success',
                            'submitted' => 'info',
                            'rejected'  => 'danger',
                            default     => 'gray',
                        }),

                    TextEntry::make('onboardingSubmission.submitted_at')
                        ->label('Submitted At')
                        ->dateTime('F j, Y g:i A')
                        ->placeholder('Not submitted'),

                    TextEntry::make('onboardingSubmission.business_name')
                        ->label('Business Name')
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.website')
                        ->label('Business Website')
                        ->url(fn ($state) => $state ?: null)
                        ->openUrlInNewTab()
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.service_area')
                        ->label('Service Area')
                        ->placeholder('—')
                        ->columnSpanFull(),

                    TextEntry::make('onboardingSubmission.primary_contact')
                        ->label('Primary Contact')
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.phone')
                        ->label('Phone')
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.ad_budget_ready')
                        ->label('Ad Budget Ready')
                        ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                        ->badge()
                        ->color(fn ($state) => $state ? 'success' : 'gray')
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.payment_method_for_ads')
                        ->label('Ad Payment Method')
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.license_original_name')
                        ->label('License File')
                        ->placeholder('Not uploaded'),
                ]),

            Section::make('Internal Notes')
                ->schema([
                    TextEntry::make('notes')
                        ->label('')
                        ->prose()
                        ->columnSpanFull()
                        ->placeholder('No notes.'),
                ]),

        ]);
    }
}
