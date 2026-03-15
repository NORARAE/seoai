<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $fillable = [
        'client_id',
        'state_id',
        'name',
        'domain',
        'status',
        'crawl_status',
        'pages_crawled',
        'last_crawled_at',
        'gsc_property_url',
        'gsc_access_token',
        'gsc_refresh_token',
        'gsc_token_expires_at',
        'gsc_last_sync_at',
        'gsc_sync_status',
        'gsc_sync_error',
        // CMS and publishing fields
        'cms_type',
        'publishing_mode',
        'publishing_status',
        'wordpress_url',
        'wordpress_username',
        'wordpress_app_password',
        'api_endpoint',
        'api_credentials',
        'last_connection_test_at',
        'connection_test_status',
        'connection_test_error',
    ];

    protected $casts = [
        'pages_crawled' => 'integer',
        'last_crawled_at' => 'datetime',
        'gsc_token_expires_at' => 'datetime',
        'gsc_last_sync_at' => 'datetime',
        'last_connection_test_at' => 'datetime',
        'api_credentials' => 'array',
    ];

    /**
     * Get the client that owns the site
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the state this site operates in
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get all pages for this site
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Get all internal links for this site
     */
    public function internalLinks(): HasMany
    {
        return $this->hasMany(InternalLink::class);
    }

    /**
     * Get all performance metrics for this site
     */
    public function performanceMetrics(): HasMany
    {
        return $this->hasMany(PerformanceMetric::class);
    }

    /**
     * Get all baseline snapshots for this site
     */
    public function baselineSnapshots(): HasMany
    {
        return $this->hasMany(BaselineSnapshot::class);
    }

    /**
     * Get all optimization runs for this site
     */
    public function optimizationRuns(): HasMany
    {
        return $this->hasMany(OptimizationRun::class);
    }

    /**
     * Check if this site is connected to Google Search Console
     */
    public function isConnectedToGsc(): bool
    {
        return !empty($this->gsc_property_url) && !empty($this->gsc_access_token);
    }

    /**
     * Check if GSC token needs refresh
     */
    public function gscTokenNeedsRefresh(): bool
    {
        if (!$this->gsc_token_expires_at) {
            return false;
        }

        return $this->gsc_token_expires_at->isPast();
    }
}
