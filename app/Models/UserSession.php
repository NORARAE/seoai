<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $fillable = [
        'session_token',
        'ip_address',
        'user_agent',
        'country',
        'city',
        'first_page',
        'last_page',
        'last_activity_at',
        'visited_book',
        'opened_booking_modal',
        'visited_onboarding',
        'first_book_at',
        'first_modal_open_at',
        'first_onboarding_at',
    ];

    protected function casts(): array
    {
        return [
            'last_activity_at' => 'datetime',
            'visited_book' => 'boolean',
            'opened_booking_modal' => 'boolean',
            'visited_onboarding' => 'boolean',
            'first_book_at' => 'datetime',
            'first_modal_open_at' => 'datetime',
            'first_onboarding_at' => 'datetime',
        ];
    }
}
