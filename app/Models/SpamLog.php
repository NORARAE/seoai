<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpamLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'inquiry_id',
        'action',
        'reason',
        'spam_risk',
        'risk_score',
        'ip_address',
        'email',
        'name',
        'company',
        'user_agent',
        'turnstile_valid',
        'turnstile_reason',
        'is_reviewed',
        'signals',
    ];

    protected function casts(): array
    {
        return [
            'signals'         => 'array',
            'risk_score'      => 'float',
            'turnstile_valid' => 'boolean',
            'is_reviewed'     => 'boolean',
        ];
    }

    /** Block this log entry's IP in the persistent blocklist. */
    public function blockIp(string $source = 'admin'): void
    {
        if ($this->ip_address) {
            \App\Models\BlockedIp::block($this->ip_address, 'admin_blocked', $source);
        }
    }

    /** Remove this log entry's IP from the persistent blocklist. */
    public function unblockIp(): void
    {
        if ($this->ip_address) {
            \App\Models\BlockedIp::where('ip_address', $this->ip_address)->delete();
        }
    }

    /** Whether this IP is currently in the persistent blocklist. */
    public function isIpBlocked(): bool
    {
        return $this->ip_address
            ? \App\Models\BlockedIp::isBlocked($this->ip_address)
            : false;
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }
}
