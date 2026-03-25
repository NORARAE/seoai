<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorScanUrl extends Model
{
    protected $fillable = [
        'site_id',
        'competitor_domain_id',
        'competitor_scan_run_id',
        'url',
        'normalized_url',
        'path',
        'source',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function competitorDomain(): BelongsTo
    {
        return $this->belongsTo(CompetitorDomain::class);
    }

    public function competitorScanRun(): BelongsTo
    {
        return $this->belongsTo(CompetitorScanRun::class);
    }
}