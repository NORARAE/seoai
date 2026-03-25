<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrawlQueue extends Model
{
    protected $table = 'crawl_queue';

    protected $fillable = [
        'site_id',
        'scan_run_id',
        'url_inventory_id',
        'url',
        'priority',
        'depth',
        'status',
        'attempts',
        'last_attempted_at',
        'discovered_from',
        'available_at',
        'error_message',
    ];

    protected $casts = [
        'priority' => 'integer',
        'depth' => 'integer',
        'attempts' => 'integer',
        'last_attempted_at' => 'datetime',
        'available_at' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function urlInventory(): BelongsTo
    {
        return $this->belongsTo(UrlInventory::class, 'url_inventory_id');
    }

    public function scanRun(): BelongsTo
    {
        return $this->belongsTo(ScanRun::class, 'scan_run_id');
    }
}
