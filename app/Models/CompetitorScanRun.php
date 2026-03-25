<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitorScanRun extends Model
{
    protected $fillable = [
        'site_id',
        'competitor_domain_id',
        'triggered_by_type',
        'initiated_by',
        'status',
        'started_at',
        'completed_at',
        'urls_discovered',
        'urls_compared',
        'gaps_found',
        'credit_consumed',
        'usage_record_id',
        'error_summary',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'urls_discovered' => 'integer',
        'urls_compared' => 'integer',
        'gaps_found' => 'integer',
        'credit_consumed' => 'boolean',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function competitorDomain(): BelongsTo
    {
        return $this->belongsTo(CompetitorDomain::class);
    }

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function urls(): HasMany
    {
        return $this->hasMany(CompetitorScanUrl::class);
    }
}