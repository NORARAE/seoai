<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteCrawlSetting extends Model
{
    protected $fillable = [
        'site_id',
        'max_pages',
        'crawl_delay',
        'max_depth',
        'obey_robots',
        'follow_nofollow',
    ];

    protected $casts = [
        'max_pages' => 'integer',
        'crawl_delay' => 'integer',
        'max_depth' => 'integer',
        'obey_robots' => 'boolean',
        'follow_nofollow' => 'boolean',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
