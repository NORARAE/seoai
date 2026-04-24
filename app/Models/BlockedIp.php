<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedIp extends Model
{
    protected $fillable = [
        'ip_address',
        'reason',
        'source',
        'blocked_at',
    ];

    protected $casts = [
        'blocked_at' => 'datetime',
    ];

    /**
     * Check whether an IP address is present in the persistent blocklist.
     */
    public static function isBlocked(string $ip): bool
    {
        return static::where('ip_address', $ip)->exists();
    }

    /**
     * Add an IP to the persistent blocklist, ignoring duplicates.
     */
    public static function block(string $ip, string $reason, string $source = 'antispam_service'): static
    {
        return static::firstOrCreate(
            ['ip_address' => $ip],
            [
                'reason' => $reason,
                'source' => $source,
                'blocked_at' => now(),
            ]
        );
    }
}
