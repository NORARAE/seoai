<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoReport extends Model
{
    protected $fillable = [
        'site_url',
        'report_type',
        'dimension',
        'date_range',
        'data',
        'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'data'       => 'array',
            'fetched_at' => 'datetime',
        ];
    }
}
