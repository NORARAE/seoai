<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuickScan extends Model
{
    protected $fillable = [
        'email',
        'url',
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
        'status',
        'emails_sent',
        'scanned_at',
    ];

    protected $casts = [
        'paid' => 'boolean',
        'emails_sent' => 'boolean',
        'score' => 'integer',
        'issues' => 'array',
        'strengths' => 'array',
        'raw_checks' => 'array',
        'scanned_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_SCANNED = 'scanned';
    const STATUS_ERROR = 'error';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
