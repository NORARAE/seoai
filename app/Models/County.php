<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class County extends Model
{
    protected $fillable = [
        'state_id',
        'name',
        'slug',
    ];

    /**
     * Get the state this county belongs to
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get all cities in this county
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
