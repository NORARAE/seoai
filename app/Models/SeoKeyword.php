<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoKeyword extends Model
{
    protected $fillable = [
        'query',
        'clicks',
        'impressions',
        'ctr',
        'position',
        'date_range',
        'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'ctr'        => 'float',
            'position'   => 'float',
            'fetched_at' => 'datetime',
        ];
    }
}
