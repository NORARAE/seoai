<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'consult_type_id',
        'booking_type',
        'name',
        'email',
        'phone',
        'company',
        'website',
        'message',
        'add_ons',
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
        'reminder_24h_sent_at',
        'reminder_2h_sent_at',
        'sms_opted_out',
        'public_booking_token',
        'reschedule_count',
        'last_rescheduled_at',
    ];

    protected function casts(): array
    {
        return [
            'booking_type' => 'string',
            'add_ons' => 'array',
            'preferred_date' => 'date',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'reminder_24h_sent_at' => 'datetime',
            'reminder_2h_sent_at' => 'datetime',
            'last_rescheduled_at' => 'datetime',
            'reschedule_count' => 'integer',
            'sms_opted_out' => 'boolean',
        ];
    }

    public function consultType(): BelongsTo
    {
        return $this->belongsTo(ConsultType::class);
    }

    public function lead(): HasOne
    {
        return $this->hasOne(Lead::class, 'booking_id');
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
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
