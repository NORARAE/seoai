<?php

namespace App\Models;

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
}
