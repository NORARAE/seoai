<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'state_id',
        'county_id',
        'city_id',
        'page_exists',
        'location_page_id',
        'traffic_potential',
        'priority_score',
        'estimated_monthly_searches',
        'avg_impressions_30d',
        'avg_clicks_30d',
        'avg_ctr_30d',
        'avg_position_30d',
        'status',
        'last_analyzed_at',
        'page_generated_at',
        'analysis_data',
    ];

    protected $casts = [
        'page_exists' => 'boolean',
        'traffic_potential' => 'integer',
        'priority_score' => 'integer',
        'estimated_monthly_searches' => 'decimal:2',
        'avg_ctr_30d' => 'decimal:4',
        'avg_position_30d' => 'decimal:2',
        'analysis_data' => 'array',
        'last_analyzed_at' => 'datetime',
        'page_generated_at' => 'datetime',
    ];

    /**
     * Get the service
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the state
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the county
     */
    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    /**
     * Get the city
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the location page (if exists)
     */
    public function locationPage(): BelongsTo
    {
        return $this->belongsTo(LocationPage::class);
    }

    /**
     * Scopes
     */
    public function scopeMissingPages($query)
    {
        return $query->where('page_exists', false);
    }

    public function scopeExistingPages($query)
    {
        return $query->where('page_exists', true);
    }

    public function scopeHighPriority($query, int $threshold = 70)
    {
        return $query->where('priority_score', '>=', $threshold);
    }

    public function scopeLowTraffic($query)
    {
        return $query->where('page_exists', true)
            ->where('avg_impressions_30d', '<', 100);
    }

    public function scopeTopOpportunities($query, int $limit = 20)
    {
        return $query->missingPages()
            ->highPriority()
            ->orderByDesc('priority_score')
            ->limit($limit);
    }

    /**
     * Get CSS color class based on status
     */
    public function getStatusColorAttribute(): string
    {
        if (!$this->page_exists) {
            return 'red'; // Missing page
        }

        if ($this->avg_impressions_30d < 100) {
            return 'yellow'; // Low traffic
        }

        return 'green'; // Active page with traffic
    }

    /**
     * Get human-readable status text
     */
    public function getStatusTextAttribute(): string
    {
        if (!$this->page_exists) {
            return 'Missing Page';
        }

        if ($this->avg_impressions_30d < 100) {
            return 'Low Traffic';
        }

        return 'Active';
    }
}
