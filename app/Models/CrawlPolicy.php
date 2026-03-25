<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrawlPolicy extends Model
{
    protected $fillable = [
        'site_id',
        'robots_txt',
        'allow_rules',
        'disallow_rules',
        'sitemap_urls',
        'crawl_delay',
        'last_fetched_at',
        'last_request_at',
    ];

    protected $casts = [
        'allow_rules' => 'array',
        'disallow_rules' => 'array',
        'sitemap_urls' => 'array',
        'crawl_delay' => 'integer',
        'last_fetched_at' => 'datetime',
        'last_request_at' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
