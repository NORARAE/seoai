<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $fillable = [
        'client_id',
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
     * Get the client that owns the site
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get all pages for this site
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Get all internal links for this site
     */
    public function internalLinks(): HasMany
    {
        return $this->hasMany(InternalLink::class);
    }
}
