<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inquiry extends Model
{
    protected $fillable = [
        'name',
        'company',
        'email',
        'website',
        'type',
        'tier',
        'niche',
        'message',
        'ip_address',
        'status',
        'welcome_sent_at',
        'admin_notified_at',
        // Enrichment fields
        'ip_city',
        'ip_region',
        'ip_country',
        'ip_isp',
        'ip_is_proxy',
        'ip_is_hosting',
        'url_status',
        'url_is_https',
        'domain_age_days',
        'email_type',
        'honeypot_triggered',
        'time_to_submit_seconds',
        'recaptcha_score',
        'spam_risk',
        'company_enrichment',
    ];

    protected function casts(): array
    {
        return [
            'welcome_sent_at'         => 'datetime',
            'admin_notified_at'       => 'datetime',
            'ip_is_proxy'             => 'boolean',
            'ip_is_hosting'           => 'boolean',
            'url_is_https'            => 'boolean',
            'honeypot_triggered'      => 'boolean',
            'recaptcha_score'         => 'float',
            'company_enrichment'      => 'array',
        ];
    }

    public function spamLogs(): HasMany
    {
        return $this->hasMany(SpamLog::class);
    }

    /** Human-readable tier labels */
    public function tierLabel(): string
    {
        return match ($this->tier) {
            'starter' => 'Starter',
            '5k'      => '$5k / mo',
            '10k'     => '$10k / mo',
            'legacy'  => 'Legacy',
            default   => ucfirst($this->tier),
        };
    }

    /** Human-readable type labels */
    public function typeLabel(): string
    {
        return match ($this->type) {
            'agency'   => 'Agency',
            'business' => 'Business',
            'both'     => 'Agency + Business',
            default    => ucfirst($this->type),
        };
    }
}
