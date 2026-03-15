<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Page extends Model
{
    protected $fillable = [
        'site_id',
        'url',
        'path',
        'title',
        'suggested_title',
        'status_code',
        'crawl_status',
        'incoming_links_count',
        'outgoing_links_count',
        'last_crawled_at',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'incoming_links_count' => 'integer',
        'outgoing_links_count' => 'integer',
        'last_crawled_at' => 'datetime',
    ];

    /**
     * Get the site that owns this page
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get internal links where this page is the source
     */
    public function outgoingLinks(): HasMany
    {
        return $this->hasMany(InternalLink::class, 'source_page_id');
    }

    /**
     * Get link suggestions where this page is the target
     */
    public function linkSuggestions(): HasMany
    {
        return $this->hasMany(LinkSuggestion::class, 'target_page_id');
    }

    /**
     * Get opportunities related to this page
     */
    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    /**
     * Scope to filter pages with missing titles
     */
    public function scopeMissingTitle($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('title')->orWhere('title', '');
        });
    }

    /**
     * Scope to filter pages with broken status codes
     */
    public function scopeBroken($query)
    {
        return $query->where('status_code', '>=', 400);
    }

    /**
     * Get performance metrics for this page
     */
    public function performanceMetrics(): HasMany
    {
        return $this->hasMany(PerformanceMetric::class);
    }

    /**
     * Get baseline snapshots for this page
     */
    public function baselineSnapshots(): MorphMany
    {
        return $this->morphMany(BaselineSnapshot::class, 'snapshotable');
    }

    /**
     * Get the most recent baseline snapshot
     */
    public function latestBaselineSnapshot(): MorphOne
    {
        return $this->morphOne(BaselineSnapshot::class, 'snapshotable')->latestOfMany('snapshot_date');
    }

    /**
     * Get optimization runs for this page
     */
    public function optimizationRuns(): MorphMany
    {
        return $this->morphMany(OptimizationRun::class, 'optimizable');
    }

    /**
     * Get content for snapshot (can be overridden in subclasses)
     */
    public function getContentForSnapshot(): string
    {
        return $this->title ?? '';
    }

    /**
     * Scope to filter pages awaiting crawl
     */
    public function scopeDiscovered($query)
    {
        return $query->where('crawl_status', 'discovered');
    }
}
