<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorGap extends Model
{
    protected $fillable = [
        'site_id',
        'competitor_domain_id',
        'site_scan_run_id',
        'competitor_scan_run_id',
        'keyword_topic',
        'search_volume',
        'competitor_domain',
        'competitor_url',
        'page_missing',
        'opportunity_score',
        'score_label',
        'status',
        'is_current',
        'evidence',
    ];

    protected $casts = [
        'search_volume' => 'integer',
        'page_missing' => 'boolean',
        'opportunity_score' => 'integer',
        'is_current' => 'boolean',
        'evidence' => 'array',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function competitorDomainRecord(): BelongsTo
    {
        return $this->belongsTo(CompetitorDomain::class, 'competitor_domain_id');
    }

    public function siteScanRun(): BelongsTo
    {
        return $this->belongsTo(ScanRun::class, 'site_scan_run_id');
    }

    public function competitorScanRun(): BelongsTo
    {
        return $this->belongsTo(CompetitorScanRun::class, 'competitor_scan_run_id');
    }
}
