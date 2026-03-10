<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $fillable = [
        'name',
        'domain',
        'status',
        'crawl_status',
        'pages_crawled',
        'last_crawled_at',
    ];

    protected $casts = [
        'pages_crawled' => 'integer',
        'last_crawled_at' => 'datetime',
    ];

    /**
     * Get all pages for this site
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }
}
