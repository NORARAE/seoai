<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'business_name',
        'website_url',
        'industry',
        'role_at_company',
        'primary_market',
        'services',
        'top_goal',
        'biggest_challenge',
    ];

    protected $casts = [
        'services' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
