<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScanRun extends Model
{
    protected $fillable = [
        'site_id',
        'triggered_by_type',
        'initiated_by',
        'crawl_mode',
        'seed_source',
        'status',
        'started_at',
        'completed_at',
        'pages_discovered',
        'pages_crawled',
        'pages_failed',
        'opportunities_found',
        'error_summary',
        'notes',
        'quick_scan_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'pages_discovered' => 'integer',
        'pages_crawled' => 'integer',
        'pages_failed' => 'integer',
        'opportunities_found' => 'integer',
    ];

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function quickScan(): BelongsTo
    {
        return $this->belongsTo(QuickScan::class);
    }

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function crawlQueueItems(): HasMany
    {
        return $this->hasMany(CrawlQueue::class, 'scan_run_id');
    }

    public function firstSeenUrls(): HasMany
    {
        return $this->hasMany(UrlInventory::class, 'first_seen_scan_run_id');
    }

    public function seoOpportunities(): HasMany
    {
        return $this->hasMany(SeoOpportunity::class, 'scan_run_id');
    }

    // ──────────────────────────────────────────────
    // Lifecycle helpers
    // ──────────────────────────────────────────────

    /**
     * Mark this run as completed with summary counts.
     * Idempotent: skips if already completed/failed/cancelled.
     */
    public function markCompleted(int $discovered, int $crawled, int $failed, int $opportunities): void
    {
        $updated = static::query()
            ->whereKey($this->id)
            ->whereIn('status', ['running', 'pending'])
            ->update([
                'status' => 'completed',
                'completed_at' => now(),
                'pages_discovered' => $discovered,
                'pages_crawled' => $crawled,
                'pages_failed' => $failed,
                'opportunities_found' => $opportunities,
                'updated_at' => now(),
            ]);

        if ($updated > 0) {
            $this->refresh();
        }
    }

    /**
     * Mark this run as failed with an error summary.
     * Idempotent: skips if already in a terminal state.
     */
    public function markFailed(string $errorSummary): void
    {
        $updated = static::query()
            ->whereKey($this->id)
            ->whereIn('status', ['running', 'pending'])
            ->update([
                'status' => 'failed',
                'completed_at' => now(),
                'error_summary' => mb_substr($errorSummary, 0, 65535),
                'updated_at' => now(),
            ]);

        if ($updated > 0) {
            $this->refresh();
        }
    }

    // ──────────────────────────────────────────────
    // Query scopes
    // ──────────────────────────────────────────────

    /** @param \Illuminate\Database\Eloquent\Builder<static> $query */
    public function scopeRunning($query): void
    {
        $query->where('status', 'running');
    }

    /** @param \Illuminate\Database\Eloquent\Builder<static> $query */
    public function scopeForSite($query, int $siteId): void
    {
        $query->where('site_id', $siteId);
    }
}
