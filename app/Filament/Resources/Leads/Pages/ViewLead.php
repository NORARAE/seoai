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
                ->visible(fn() => $this->record->onboarding_status === 'submitted')
                ->requiresConfirmation()
                ->modalHeading('Approve this onboarding?')
                ->modalDescription('The lead will be marked as approved. Use "Activate Client" once their account is set up.')
                ->action(function () {
                    $this->record->update([
                        'onboarding_status' => 'approved',
                        'lifecycle_stage' => Lead::STAGE_APPROVED,
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
                ->visible(fn() => $this->record->onboarding_status === 'approved'
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
                ->visible(fn() => in_array($this->record->onboarding_status, ['submitted', 'approved']))
                ->requiresConfirmation()
                ->modalHeading('Reject this onboarding?')
                ->modalDescription('The lead will be marked as rejected.')
                ->action(function () {
                    $this->record->update([
                        'onboarding_status' => 'rejected',
                        'lifecycle_stage' => Lead::STAGE_REJECTED,
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
                ->visible(fn() => $this->record->onboardingSubmission?->hasLicense())
                ->url(fn() => route('onboarding.license.download', [
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
                        ->url(fn($state) => $state ?: null)
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
                        ->color(fn(?string $state): string => match ($state) {
                            Lead::STAGE_ACTIVE => 'success',
                            Lead::STAGE_APPROVED => 'success',
                            Lead::STAGE_ONBOARDING_SUBMITTED => 'info',
                            Lead::STAGE_PAID => 'warning',
                            Lead::STAGE_BOOKED => 'gray',
                            Lead::STAGE_REJECTED => 'danger',
                            Lead::STAGE_LOST => 'danger',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn(?string $state) => match ($state) {
                            Lead::STAGE_ONBOARDING_SUBMITTED => 'Onboarding Submitted',
                            default => ucwords(str_replace('_', ' ', $state ?? 'new')),
                        }),
                    TextEntry::make('session_type')->label('Session Type')->placeholder('—'),
                    TextEntry::make('payment_status')->label('Payment')
                        ->badge()
                        ->color(fn(?string $state): string => match ($state) {
                            'paid' => 'success',
                            'free' => 'info',
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
                        ->color(fn(?string $state): string => match ($state) {
                            'confirmed' => 'success',
                            'awaiting_payment' => 'warning',
                            'cancelled' => 'danger',
                            default => 'gray',
                        })
                        ->placeholder('—'),
                ]),

            Section::make('Onboarding')
                ->columns(2)
                ->schema([
                    TextEntry::make('onboarding_status')
                        ->label('Status')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'approved' => 'success',
                            'submitted' => 'info',
                            'rejected' => 'danger',
                            default => 'gray',
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
                        ->url(fn($state) => $state ?: null)
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
                        ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                        ->badge()
                        ->color(fn($state) => $state ? 'success' : 'gray')
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.payment_method_for_ads')
                        ->label('Ad Payment Method')
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.license_original_name')
                        ->label('License File')
                        ->placeholder('Not uploaded'),

                    TextEntry::make('onboardingSubmission.platform_type')
                        ->label('Website Platform')
                        ->formatStateUsing(fn($state) => match ($state) {
                            'wordpress' => 'WordPress',
                            'shopify' => 'Shopify',
                            'other' => 'Other / Custom',
                            default => '—',
                        })
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.access_method')
                        ->label('Access Method')
                        ->formatStateUsing(fn($state) => match ($state) {
                            'invite_email' => 'Invite via email',
                            'provide_later' => 'Will provide later',
                            'need_help' => 'Needs help',
                            default => '—',
                        })
                        ->badge()
                        ->color(fn($state) => match ($state) {
                            'invite_email' => 'success',
                            'provide_later' => 'warning',
                            'need_help' => 'info',
                            default => 'gray',
                        })
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.analytics_access')
                        ->label('GA4 Access')
                        ->formatStateUsing(fn($state) => $state ? 'Has GA4' : 'No / Unsure')
                        ->badge()
                        ->color(fn($state) => $state ? 'success' : 'gray')
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.search_console_access')
                        ->label('Search Console')
                        ->formatStateUsing(fn($state) => $state ? 'Has GSC' : 'No / Unsure')
                        ->badge()
                        ->color(fn($state) => $state ? 'success' : 'gray')
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.add_ons')
                        ->label('Requested Add-ons')
                        ->formatStateUsing(fn($state) => $state
                            ? implode(', ', array_map(fn($s) => match ($s) {
                                'local_seo_setup' => 'Local SEO Setup',
                                'google_ads_setup' => 'Google Ads Setup',
                                'monthly_reporting' => 'Monthly Reporting',
                                'competitor_analysis' => 'Competitor Analysis',
                                default => $s,
                            }, (array) $state))
                            : 'None selected')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),

            Section::make('Routing & Commitment')
                ->columns(2)
                ->schema([
                    TextEntry::make('onboardingSubmission.lead_type')
                        ->label('Lead Type')
                        ->formatStateUsing(fn($state) => match ($state) {
                            'single_location' => 'Single Location',
                            'multi_location' => 'Multi-Location',
                            'agency' => 'Agency / Partner',
                            default => '—',
                        })
                        ->badge()
                        ->color(fn($state) => match ($state) {
                            'single_location' => 'gray',
                            'multi_location' => 'info',
                            'agency' => 'warning',
                            default => 'gray',
                        })
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.number_of_locations')
                        ->label('No. of Locations')
                        ->formatStateUsing(fn($state) => match ($state) {
                            '1' => '1',
                            '2_to_5' => '2–5',
                            '6_to_10' => '6–10',
                            '11_to_20' => '11–20',
                            '20_plus' => '20+',
                            default => '—',
                        })
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.offer_path')
                        ->label('Offer Path')
                        ->formatStateUsing(fn($state) => match ($state) {
                            'core' => 'Core',
                            'growth' => 'Growth',
                            'multi_market' => 'Multi-Market',
                            'agency' => 'Agency–Partner',
                            default => '—',
                        })
                        ->badge()
                        ->color(fn($state) => match ($state) {
                            'core' => 'gray',
                            'growth' => 'info',
                            'multi_market' => 'warning',
                            'agency' => 'danger',
                            default => 'gray',
                        })
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.rollout_scope')
                        ->label('Rollout Scope')
                        ->formatStateUsing(fn($state) => match ($state) {
                            'single' => 'Single',
                            'multi' => 'Multi',
                            'enterprise' => 'Enterprise',
                            default => '—',
                        })
                        ->badge()
                        ->color(fn($state) => match ($state) {
                            'single' => 'gray',
                            'multi' => 'info',
                            'enterprise' => 'warning',
                            default => 'gray',
                        })
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.commitment_length')
                        ->label('Commitment Length')
                        ->formatStateUsing(fn($state) => match ($state) {
                            '4_month' => '4 months',
                            '3_month' => '3 months',
                            '2_month' => '2 months',
                            default => '—',
                        })
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.payment_structure')
                        ->label('Payment Structure')
                        ->formatStateUsing(fn($state) => match ($state) {
                            'full_prepay' => 'Full Prepay',
                            '50_50_split' => '50/50 Split',
                            'activation_plus_subscription' => 'Activation + Subscription',
                            default => '—',
                        })
                        ->badge()
                        ->color(fn($state) => match ($state) {
                            'full_prepay' => 'success',
                            '50_50_split' => 'info',
                            'activation_plus_subscription' => 'warning',
                            default => 'gray',
                        })
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.agency_review_required')
                        ->label('Agency Review Required')
                        ->formatStateUsing(fn($state) => $state ? 'Yes — requires review' : 'No')
                        ->badge()
                        ->color(fn($state) => $state ? 'warning' : 'gray')
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.ads_management_required')
                        ->label('Ads Management')
                        ->formatStateUsing(fn($state) => $state ? 'Required' : 'Not required')
                        ->badge()
                        ->color(fn($state) => $state ? 'info' : 'gray')
                        ->placeholder('—'),

                    TextEntry::make('onboardingSubmission.ads_account_control')
                        ->label('Ads Account Control')
                        ->formatStateUsing(fn($state) => match ($state) {
                            'client_owned' => 'Client-owned',
                            'shared_access' => 'Shared access',
                            'not_configured' => 'Not configured',
                            default => '—',
                        })
                        ->placeholder('—'),
                ]),

            Section::make('Revenue Intelligence')
                ->columns(2)
                ->schema([
                    // ── Tier classification ──────────────────────────────────
                    TextEntry::make('recommended_tier_label')
                        ->label('Recommended Tier')
                        ->getStateUsing(fn($record) => match ($record->onboardingSubmission?->recommended_tier) {
                            'core'                  => 'Core Build Candidate',
                            'multi_market_standard' => 'Multi-Market Rollout',
                            'multi_market_custom'   => 'Custom Deployment',
                            'agency_partner'        => 'Agency / Partner',
                            default                 => '—',
                        })
                        ->badge()
                        ->color(fn($state) => match ($state) {
                            'Core Build Candidate'  => 'gray',
                            'Multi-Market Rollout'  => 'info',
                            'Custom Deployment'     => 'warning',
                            'Agency / Partner'      => 'danger',
                            default                 => 'gray',
                        }),

                    TextEntry::make('booking_priority_label')
                        ->label('Booking Priority')
                        ->getStateUsing(fn($record) => match ($record->onboardingSubmission?->booking_priority) {
                            'high_value'     => 'High Value',
                            'partner_review' => 'Partner Review',
                            default          => 'Standard',
                        })
                        ->badge()
                        ->color(fn($state) => match ($state) {
                            'High Value'     => 'warning',
                            'Partner Review' => 'danger',
                            default          => 'gray',
                        }),

                    // ── Commitment & revenue structure ───────────────────────
                    TextEntry::make('revenue_structure')
                        ->label('Revenue Structure')
                        ->getStateUsing(fn($record) => match ($record->onboardingSubmission?->payment_structure) {
                            'activation_plus_subscription' => 'Activation + Monthly Subscription',
                            '50_50_split'                  => '50/50 Split (one-time)',
                            'full_prepay'                  => 'Full Prepay (one-time)',
                            default                        => 'Not yet determined',
                        })
                        ->badge()
                        ->color(fn($state) => match ($state) {
                            'Activation + Monthly Subscription' => 'success',
                            '50/50 Split (one-time)'            => 'info',
                            'Full Prepay (one-time)'            => 'gray',
                            default                             => 'gray',
                        }),

                    TextEntry::make('commitment_length_label')
                        ->label('Commitment Length')
                        ->getStateUsing(fn($record) => match ($record->onboardingSubmission?->commitment_length) {
                            '4_month' => '4-month structured cycle',
                            '3_month' => '3 months',
                            '2_month' => '2 months',
                            default   => '4-month structured cycle',
                        }),

                    // ── Stripe tier mapping ──────────────────────────────────
                    TextEntry::make('stripe_tier_mapping')
                        ->label('Stripe Tier')
                        ->getStateUsing(fn($record) => match ($record->onboardingSubmission?->recommended_tier) {
                            'multi_market_standard', 'multi_market_custom' => 'Multi',
                            'agency_partner'                               => 'Agency',
                            default                                        => 'Core',
                        })
                        ->badge()
                        ->color(fn($state) => match ($state) {
                            'Multi'   => 'info',
                            'Agency'  => 'danger',
                            default   => 'gray',
                        }),

                    // ── Value range ──────────────────────────────────────────
                    TextEntry::make('estimated_value_range')
                        ->label('Estimated Value Range')
                        ->getStateUsing(fn($record) => $record->onboardingSubmission?->estimated_value_range ?? '—'),

                    // ── Close control ────────────────────────────────────────
                    TextEntry::make('positioning_signal')
                        ->label('Positioning Signal')
                        ->getStateUsing(fn($record) => $record->onboardingSubmission?->positioning_signal ?? '—')
                        ->badge()
                        ->color(fn($state) => match ($state) {
                            'System-level deployment' => 'danger',
                            'Market expansion'        => 'warning',
                            'Growth-stage'            => 'info',
                            default                   => 'gray',
                        }),

                    TextEntry::make('booking_intent_strength')
                        ->label('Booking Intent')
                        ->getStateUsing(fn($record) => $record->onboardingSubmission?->booking_intent_strength ?? '—')
                        ->badge()
                        ->color(fn($state) => match ($state) {
                            'partner-level'    => 'danger',
                            'expansion-ready'  => 'warning',
                            'serious'          => 'info',
                            default            => 'gray',
                        }),

                    TextEntry::make('recommended_close_style')
                        ->label('Close Style')
                        ->getStateUsing(fn($record) => $record->onboardingSubmission?->recommended_close_style ?? '—'),

                    TextEntry::make('deal_floor_enforced')
                        ->label('Deal Floor Enforced')
                        ->getStateUsing(fn($record) => $record->onboardingSubmission?->deal_floor_enforced ? 'Yes — do not close below tier floor' : 'No')
                        ->badge()
                        ->color(fn($state) => str_starts_with($state, 'Yes') ? 'warning' : 'gray'),

                    // ── Risk flags ───────────────────────────────────────────
                    TextEntry::make('ads_risk_flag')
                        ->label('Ads Risk')
                        ->getStateUsing(fn($record) => $record->onboardingSubmission?->ads_risk_note ?? 'None identified')
                        ->columnSpanFull()
                        ->color(fn($state) => $state !== 'None identified' ? 'warning' : 'gray'),

                    TextEntry::make('churn_risk_flag')
                        ->label('Churn Risk')
                        ->getStateUsing(function ($record) {
                            $sub = $record->onboardingSubmission;
                            if (!$sub) {
                                return 'Unknown — no submission';
                            }
                            $flags = [];
                            if ($sub->commitment_length && $sub->commitment_length !== '4_month') {
                                $flags[] = 'Short commitment selected';
                            }
                            if ($sub->payment_structure === 'full_prepay' && $sub->recommended_tier !== 'core') {
                                $flags[] = 'One-time on high-value tier — no recurring lock-in';
                            }
                            if ($sub->ads_management_required && $sub->ads_account_control === 'not_configured') {
                                $flags[] = 'Ads required but account control unresolved';
                            }
                            return empty($flags) ? 'Low' : implode(' · ', $flags);
                        })
                        ->columnSpanFull()
                        ->color(fn($state) => $state === 'Low' ? 'success' : 'warning'),

                    // ── Suggested close ──────────────────────────────────────
                    TextEntry::make('suggested_next_step')
                        ->label('Suggested Next Step')
                        ->getStateUsing(fn($record) => $record->onboardingSubmission?->suggested_next_step ?? '—')
                        ->columnSpanFull(),
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
