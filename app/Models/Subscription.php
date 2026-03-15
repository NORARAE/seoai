<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'plan_id',
        'status',
        'billing_cycle',
        'amount',
        'currency',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'canceled_at',
        'next_billing_date',
        'stripe_subscription_id',
        'stripe_customer_id',
        'payment_metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'trial_ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'next_billing_date' => 'datetime',
        'payment_metadata' => 'array',
    ];

    /**
     * Get the client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the plan
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get usage records
     */
    public function usageRecords(): HasMany
    {
        return $this->hasMany(UsageRecord::class);
    }

    /**
     * Get invoices
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTrial($query)
    {
        return $query->where('status', 'trial');
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', 'canceled');
    }

    /**
     * Check if subscription is on trial
     */
    public function onTrial(): bool
    {
        return $this->status === 'trial' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    /**
     * Check if subscription is active (including trial)
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trial']);
    }

    /**
     * Cancel subscription
     */
    public function cancel(): bool
    {
        return $this->update([
            'status' => 'canceled',
            'canceled_at' => now(),
        ]);
    }
}
