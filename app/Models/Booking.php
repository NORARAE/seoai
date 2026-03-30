<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'consult_type_id',
        'name',
        'email',
        'phone',
        'company',
        'website',
        'message',
        'preferred_date',
        'preferred_time',
        'status',
        'google_event_id',
        'google_meet_link',
        'confirmed_at',
        'cancelled_at',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'reminder_sent_at',
        'sms_opted_out',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date'   => 'date',
            'confirmed_at'     => 'datetime',
            'cancelled_at'     => 'datetime',
            'reminder_sent_at' => 'datetime',
            'sms_opted_out'    => 'boolean',
        ];
    }

    public function consultType(): BelongsTo
    {
        return $this->belongsTo(ConsultType::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isAwaitingPayment(): bool
    {
        return $this->status === 'awaiting_payment';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
