<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    protected $fillable = [
        'state_id',
        'county_id',
        'name',
        'slug',
        'latitude',
        'longitude',
        'population',
        'is_county_seat',
        'is_priority',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'population' => 'integer',
        'is_county_seat' => 'boolean',
        'is_priority' => 'boolean',
    ];

    /**
     * Get the state this city belongs to
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the county this city belongs to
     */
    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }
}
