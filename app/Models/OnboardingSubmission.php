<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingSubmission extends Model
{
    protected $fillable = [
        'lead_id',
        'booking_id',
        'business_name',
        'website',
        'service_area',
        'license_path',
        'license_original_name',
        'license_size_bytes',
        'license_mime',
        'primary_contact',
        'phone',
        'ad_budget_ready',
        'payment_method_for_ads',
        'analytics_access',
        'search_console_access',
        'platform_type',
        'access_method',
        'add_ons',
        'goals',
        'challenges',
        'growth_intent',
        'ads_status',
        'rd_referral_interest',
        'lead_type',
        'number_of_locations',
        'commitment_length',
        'payment_structure',
        'offer_path',
        'rollout_scope',
        'agency_review_required',
        'ads_management_required',
        'ads_account_control',
        'years_in_business',
        'team_size',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'ad_budget_ready' => 'boolean',
            'analytics_access' => 'boolean',
            'search_console_access' => 'boolean',
            'add_ons' => 'array',
            'rd_referral_interest' => 'boolean',
            'agency_review_required' => 'boolean',
            'ads_management_required' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function hasLicense(): bool
    {
        return (bool) $this->license_path;
    }

    // ── Computed pricing intelligence (admin-only, never stored) ─────────────

    public function recommendedTier(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->lead_type === 'agency') {
                    return 'agency_partner';
                }
                if ($this->lead_type === 'multi_location') {
                    return in_array($this->number_of_locations, ['6_to_10', '11_to_20', '20_plus'])
                        ? 'multi_market_custom'
                        : 'multi_market_standard';
                }
                return 'core';
            }
        );
    }

    public function estimatedValueRange(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->recommended_tier) {
                'multi_market_standard' => '$8K–$14K structured rollout',
                'multi_market_custom' => '$14K–$18K custom deployment',
                'agency_partner' => '$18K–$25K+ system-level deployment',
                default => '$3K–$8K entry',
            }
        );
    }

    public function bookingPriority(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->recommended_tier) {
                'agency_partner' => 'partner_review',
                'multi_market_standard', 'multi_market_custom' => 'high_value',
                default => 'standard',
            }
        );
    }

    public function adsRiskNote(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->ads_management_required) {
                    return null;
                }
                $control = $this->ads_account_control ?? 'not_configured';
                if ($control === 'not_configured') {
                    return 'Ads must run through controlled account or prepaid structure. Account control not yet confirmed.';
                }
                if ($control === 'shared_access') {
                    return 'Ads running on shared access — confirm spend authority and budget structure before activation.';
                }
                return null; // client_owned is resolved
            }
        );
    }

    // Part 1 — directive close language
    public function suggestedNextStep(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->recommended_tier) {
                'agency_partner' => 'Move directly into partner-level engagement — do not scope small',
                'multi_market_custom',
                'multi_market_standard' => 'Frame as structured rollout — do not present single-site option',
                default => 'Establish clarity, then position expansion immediately',
            }
        );
    }

    // Part 2 — deal floor enforcement signal
    public function dealFloorEnforced(): Attribute
    {
        return Attribute::make(
            get: fn() => in_array($this->recommended_tier, [
                'multi_market_standard',
                'multi_market_custom',
                'agency_partner',
            ])
        );
    }

    // Part 3 — positioning signal
    public function positioningSignal(): Attribute
    {
        return Attribute::make(
            get: function () {
                $tier = $this->recommended_tier;
                if ($tier === 'agency_partner') {
                    return 'System-level deployment';
                }
                if ($tier === 'multi_market_custom') {
                    return 'Market expansion';
                }
                if ($tier === 'multi_market_standard') {
                    return 'Growth-stage';
                }
                return 'Entry-level';
            }
        );
    }

    // Part 4 — booking intent strength
    public function bookingIntentStrength(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->lead_type === 'agency') {
                    return 'partner-level';
                }
                if ($this->lead_type === 'multi_location') {
                    $addOns = $this->add_ons ?? [];
                    $hasExpansionAddOn = count($addOns) > 0;
                    return $hasExpansionAddOn ? 'expansion-ready' : 'serious';
                }
                // single_location: commitment_length or add-ons signal seriousness
                $addOns = $this->add_ons ?? [];
                if ($this->commitment_length === '4_month' && count($addOns) > 0) {
                    return 'serious';
                }
                return 'exploratory';
            }
        );
    }

    // Part 5 — recommended close style
    public function recommendedCloseStyle(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->recommended_tier) {
                'agency_partner' => 'Partner acquisition close',
                'multi_market_custom',
                'multi_market_standard' => 'Structured rollout close',
                default => 'Consultative close',
            }
        );
    }

    public function expansionOpportunity(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (in_array($this->lead_type, ['multi_location', 'agency'])) {
                    return true;
                }
                $addOns = $this->add_ons ?? [];
                if (is_array($addOns) && count($addOns) >= 2) {
                    return true;
                }
                return false;
            }
        );
    }

    public function upsellPath(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->lead_type === 'agency') {
                    return 'agency';
                }
                if ($this->lead_type === 'multi_location') {
                    return in_array($this->number_of_locations, ['6_to_10', '11_to_20', '20_plus'])
                        ? 'agency'
                        : 'multi';
                }
                // Single-location: high add-on interest signals multi potential
                $addOns = $this->add_ons ?? [];
                if (is_array($addOns) && count($addOns) >= 2) {
                    return 'multi';
                }
                return 'core';
            }
        );
    }
}

