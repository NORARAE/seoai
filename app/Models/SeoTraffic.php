<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoTraffic extends Model
{
    protected $fillable = [
        'source',
        'sessions',
        'users',
        'pageviews',
        'bounce_rate',
        'date_range',
        'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'bounce_rate' => 'float',
            'fetched_at'  => 'datetime',
        ];
    }
}
