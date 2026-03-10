<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
