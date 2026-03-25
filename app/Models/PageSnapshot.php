<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSnapshot extends Model
{
    protected $table = 'page_snapshots';

    protected $fillable = [
        'url_id',
        'content_hash',
        'snapshot_date',
    ];

    protected $casts = [
        'snapshot_date' => 'datetime',
    ];

    public function url(): BelongsTo
    {
        return $this->belongsTo(UrlInventory::class, 'url_id');
    }
}
