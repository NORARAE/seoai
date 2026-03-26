<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsultType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'duration_minutes',
        'price',
        'is_free',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_free' => 'boolean',
            'is_active' => 'boolean',
            'duration_minutes' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function formattedPrice(): string
    {
        return $this->is_free ? 'Free' : '$' . number_format($this->price, 0);
    }

    public function formattedDuration(): string
    {
        return $this->duration_minutes . ' min';
    }
}
