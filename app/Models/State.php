<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    protected $fillable = [
        'name',
        'code',
        'slug',
    ];

    /**
     * Get all counties in this state
     */
    public function counties(): HasMany
    {
        return $this->hasMany(County::class);
    }

    /**
     * Get all cities in this state
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
