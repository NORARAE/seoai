<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'name',
        'company',
        'email',
        'website',
        'type',
        'tier',
        'niche',
        'message',
        'ip_address',
        'status',
        'welcome_sent_at',
        'admin_notified_at',
    ];

    protected function casts(): array
    {
        return [
            'welcome_sent_at'    => 'datetime',
            'admin_notified_at'  => 'datetime',
        ];
    }

    /** Human-readable tier labels */
    public function tierLabel(): string
    {
        return match ($this->tier) {
            'starter' => 'Starter',
            '5k'      => '$5k / mo',
            '10k'     => '$10k / mo',
            'legacy'  => 'Legacy',
            default   => ucfirst($this->tier),
        };
    }

    /** Human-readable type labels */
    public function typeLabel(): string
    {
        return match ($this->type) {
            'agency'   => 'Agency',
            'business' => 'Business',
            'both'     => 'Agency + Business',
            default    => ucfirst($this->type),
        };
    }
}
