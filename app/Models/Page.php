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
        'last_crawled_at',
    ];

    protected $casts = [
        'status_code' => 'integer',
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

    /**
     * Count incoming internal links to this page
     */
    public function getIncomingLinksCountAttribute(): int
    {
        return InternalLink::where('target_url', $this->url)
            ->where('site_id', $this->site_id)
            ->count();
    }
}
