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
}
