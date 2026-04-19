<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEntitlement extends Model
{
    use HasFactory;

    public const SCAN = 'scan';
    public const SIGNAL = 'signal';
    public const LEVERAGE = 'leverage';
    public const ACTIVATION = 'activation';

    protected $fillable = [
        'user_id',
        'entitlement_key',
        'status',
        'granted_at',
        'last_seen_at',
        'source_type',
        'source_id',
        'source_ref',
        'metadata',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
