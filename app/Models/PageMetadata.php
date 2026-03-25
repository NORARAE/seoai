<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageMetadata extends Model
{
    protected $table = 'page_metadata';

    protected $fillable = [
        'url_id',
        'title',
        'meta_description',
        'canonical',
        'h1',
        'h2s',
        'meta_robots',
        'schema',
    ];

    protected $casts = [
        'h2s' => 'array',
        'schema' => 'array',
    ];

    public function url(): BelongsTo
    {
        return $this->belongsTo(UrlInventory::class, 'url_id');
    }
}
