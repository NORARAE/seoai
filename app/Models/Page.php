<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
