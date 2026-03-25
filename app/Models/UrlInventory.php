<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UrlInventory extends Model
{
    protected $table = 'url_inventory';

    protected $fillable = [
        'site_id',
        'first_seen_scan_run_id',
        'last_seen_scan_run_id',
        'url',
        'normalized_url',
        'path',
        'depth',
        'discovered_from',
        'discovery_method',
        'status',
        'last_crawled_at',
        'content_hash',
        'word_count',
        'indexability_status',
        'page_type',
        'crawl_priority',
        'internal_link_count',
        'incoming_link_count',
        'is_orphan_page',
    ];

    protected $casts = [
        'depth' => 'integer',
        'last_crawled_at' => 'datetime',
        'word_count' => 'integer',
        'crawl_priority' => 'integer',
        'internal_link_count' => 'integer',
        'incoming_link_count' => 'integer',
        'is_orphan_page' => 'boolean',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function discoveredFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'discovered_from');
    }

    public function discoveredChildren(): HasMany
    {
        return $this->hasMany(self::class, 'discovered_from');
    }

    public function metadata(): HasOne
    {
        return $this->hasOne(PageMetadata::class, 'url_id');
    }

    public function content(): HasOne
    {
        return $this->hasOne(PageContent::class, 'url_id');
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany('App\\Models\\PageSnapshot', 'url_id');
    }

    public function outgoingLinks(): HasMany
    {
        return $this->hasMany(InternalLink::class, 'source_url_id');
    }

    public function incomingLinks(): HasMany
    {
        return $this->hasMany(InternalLink::class, 'target_url_id');
    }

    public function firstSeenScanRun(): BelongsTo
    {
        return $this->belongsTo(ScanRun::class, 'first_seen_scan_run_id');
    }

    public function lastSeenScanRun(): BelongsTo
    {
        return $this->belongsTo(ScanRun::class, 'last_seen_scan_run_id');
    }

}
