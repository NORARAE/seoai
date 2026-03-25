<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'sitemap_enabled',
        'sitemap_include_payload_pages',
        'sitemap_include_discovered_pages',
        'sitemap_manual_include_urls',
        'sitemap_manual_exclude_urls',
        'sitemap_max_urls_per_file',
        'gsc_last_sitemap_submission_at',
        'gsc_last_sitemap_submission_status',
        'gsc_last_sitemap_submission_error',
    ];

    protected $casts = [
        'pages_crawled' => 'integer',
        'last_crawled_at' => 'datetime',
        'gsc_token_expires_at' => 'datetime',
        'gsc_last_sync_at' => 'datetime',
        'last_connection_test_at' => 'datetime',
        'api_credentials' => 'array',
        'sitemap_enabled' => 'boolean',
        'sitemap_include_payload_pages' => 'boolean',
        'sitemap_include_discovered_pages' => 'boolean',
        'sitemap_max_urls_per_file' => 'integer',
        'gsc_last_sitemap_submission_at' => 'datetime',
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

    public function crawlSetting(): HasOne
    {
        return $this->hasOne(SiteCrawlSetting::class);
    }

    public function crawlPolicy(): HasOne
    {
        return $this->hasOne(CrawlPolicy::class);
    }

    public function urlInventory(): HasMany
    {
        return $this->hasMany(UrlInventory::class);
    }

    public function crawlQueueItems(): HasMany
    {
        return $this->hasMany(CrawlQueue::class);
    }

    public function competitorDomains(): HasMany
    {
        return $this->hasMany(CompetitorDomain::class);
    }

    public function competitorScanRuns(): HasMany
    {
        return $this->hasMany(CompetitorScanRun::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'site_user')->withTimestamps();
    }

    public function setDomainAttribute(?string $value): void
    {
        $normalized = trim(mb_strtolower((string) $value));
        $normalized = preg_replace('#^https?://#', '', $normalized) ?? $normalized;
        $normalized = preg_replace('#/.*$#', '', $normalized) ?? $normalized;

        $this->attributes['domain'] = $normalized;
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

    public function getSitemapIndexUrlAttribute(): string
    {
        return route('public.sitemaps.index', ['site' => $this]);
    }

    /**
     * @return array<int, string>
     */
    public function sitemapManualIncludeUrlList(): array
    {
        return $this->parseSitemapUrlList($this->sitemap_manual_include_urls);
    }

    /**
     * @return array<int, string>
     */
    public function sitemapManualExcludeUrlList(): array
    {
        return $this->parseSitemapUrlList($this->sitemap_manual_exclude_urls);
    }

    /**
     * @return array<int, string>
     */
    protected function parseSitemapUrlList(?string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $value) ?: [])
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
