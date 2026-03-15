<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'yearly_price',
        'currency',
        'max_sites',
        'max_pages',
        'max_ai_operations_per_month',
        'max_users',
        'has_api_access',
        'has_white_label',
        'has_priority_support',
        'features',
        'is_active',
        'is_public',
        'sort_order',
        'stripe_monthly_price_id',
        'stripe_yearly_price_id',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'features' => 'array',
        'has_api_access' => 'boolean',
        'has_white_label' => 'boolean',
        'has_priority_support' => 'boolean',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

    /**
     * Get subscriptions for this plan
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get formatted monthly price
     */
    public function getFormattedMonthlyPriceAttribute(): string
    {
        return number_format($this->monthly_price, 2);
    }

    /**
     * Get formatted yearly price
     */
    public function getFormattedYearlyPriceAttribute(): string
    {
        return number_format($this->yearly_price, 2);
    }

    /**
     * Calculate yearly savings
     */
    public function getYearlySavingsAttribute(): float
    {
        return ($this->monthly_price * 12) - $this->yearly_price;
    }
}
