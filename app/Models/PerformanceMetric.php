<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PerformanceMetric extends Model
{
    protected $fillable = [
        'site_id',
        'page_id',
        'location_page_id',
        'url',
        'query',
        'date',
        'clicks',
        'impressions',
        'ctr',
        'average_position',
        'device',
        'country',
    ];

    protected $casts = [
        'date' => 'date',
        'clicks' => 'integer',
        'impressions' => 'integer',
        'ctr' => 'decimal:4',
        'average_position' => 'decimal:2',
    ];

    /**
     * Get the site this metric belongs to
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the page this metric belongs to (if resolved)
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the location page this metric belongs to (if resolved)
     */
    public function locationPage(): BelongsTo
    {
        return $this->belongsTo(LocationPage::class);
    }

    /**
     * Get the resolvable record (Page or LocationPage) if exists
     */
    public function resolvable(): ?Model
    {
        if ($this->page_id) {
            return $this->page;
        }
        
        if ($this->location_page_id) {
            return $this->locationPage;
        }
        
        return null;
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope to find high-impression, low-CTR opportunities
     */
    public function scopeLowCtrOpportunities($query, int $minImpressions = 1000, float $maxCtr = 0.03)
    {
        return $query->where('impressions', '>=', $minImpressions)
            ->where('ctr', '<=', $maxCtr)
            ->orderByDesc('impressions');
    }

    /**
     * Scope to aggregate by URL
     */
    public function scopeGroupedByUrl($query)
    {
        return $query->selectRaw('
                url,
                site_id,
                SUM(clicks) as total_clicks,
                SUM(impressions) as total_impressions,
                AVG(ctr) as avg_ctr,
                AVG(average_position) as avg_position
            ')
            ->groupBy('url', 'site_id');
    }
}
