<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_key',
        'customer_email',
        'customer_name',
        'site_url',
        'plan',
        'urls_allowed',
        'stripe_subscription_id',
        'stripe_customer_id',
        'payment_method',
        'crypto_charge_id',
        'status',
        'trial_ends_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'expires_at' => 'datetime',
            'urls_allowed' => 'integer',
        ];
    }

    public function validations(): HasMany
    {
        return $this->hasMany(LicenseValidation::class, 'license_key', 'license_key');
    }
}