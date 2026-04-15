<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuickScan extends Model
{
    protected $fillable = [
        'email',
        'url',
        'domain',
        'url_input',
        'ip_address',
        'user_id',
        'stripe_session_id',
        'paid',
        'score',
        'issues',
        'strengths',
        'fastest_fix',
        'raw_checks',
        'categories',
        'page_count',
        'broken_links',
        'status',
        'emails_sent',
        'is_internal',
        'source',
        'suppress_emails',
        'is_repeat_scan',
        'domain_scan_count',
        'initiated_by',
        'scanned_at',
        'upgrade_plan',
        'upgrade_status',
        'upgrade_stripe_session_id',
        'upgraded_at',
        'onboarding_submission_id',
    ];

    protected $casts = [
        'paid' => 'boolean',
        'emails_sent' => 'boolean',
        'is_internal' => 'boolean',
        'suppress_emails' => 'boolean',
        'is_repeat_scan' => 'boolean',
        'domain_scan_count' => 'integer',
        'score' => 'integer',
        'issues' => 'array',
        'strengths' => 'array',
        'raw_checks' => 'array',
        'categories' => 'array',
        'broken_links' => 'array',
        'page_count' => 'integer',
        'scanned_at' => 'datetime',
        'upgraded_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_SCANNED = 'scanned';
    const STATUS_ERROR = 'error';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function onboardingSubmission(): BelongsTo
    {
        return $this->belongsTo(OnboardingSubmission::class);
    }

    public function isUpgraded(): bool
    {
        return $this->upgrade_plan !== null && $this->upgrade_status === 'paid';
    }

    public function domain(): string
    {
        return parse_url($this->url, PHP_URL_HOST) ?? $this->url;
    }
}
