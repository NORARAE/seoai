<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = [
        'site_id',
        'url',
        'path',
        'title',
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
     * Scope to filter pages awaiting crawl
     */
    public function scopeDiscovered($query)
    {
        return $query->where('crawl_status', 'discovered');
    }
}
