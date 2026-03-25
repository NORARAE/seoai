<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitorDomain extends Model
{
    protected $fillable = [
        'site_id',
        'domain',
        'scan_count',
        'paid_scan_credits',
        'last_scanned_at',
    ];

    protected $casts = [
        'scan_count' => 'integer',
        'paid_scan_credits' => 'integer',
        'last_scanned_at' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function scanRuns(): HasMany
    {
        return $this->hasMany(CompetitorScanRun::class);
    }

    public function scanUrls(): HasMany
    {
        return $this->hasMany(CompetitorScanUrl::class);
    }

    public function gaps(): HasMany
    {
        return $this->hasMany(CompetitorGap::class);
    }
}
