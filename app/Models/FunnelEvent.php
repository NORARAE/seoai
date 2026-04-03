<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;

class FunnelEvent extends Model
{
    protected $fillable = [
        'event_name',
        'session_token',
        'booking_id',
        'lead_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    // ── Event name constants ──────────────────────────────────────────────────
    public const ONBOARDING_STARTED = 'onboarding_started';
    public const ONBOARDING_COMPLETED = 'onboarding_completed';
    public const BOOKING_VIEWED = 'booking_viewed';
    public const BOOKING_CREATED = 'booking_created';
    public const BOOKING_PAID = 'booking_paid';

    // ── Relationships ─────────────────────────────────────────────────────────

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    // ── Factory helper ────────────────────────────────────────────────────────

    /**
     * Fire a funnel event, silently catching any DB errors so a tracking
     * failure never interrupts the user-facing request.
     */
    public static function fire(
        string $eventName,
        ?int $bookingId = null,
        ?int $leadId = null,
        array $metadata = []
    ): void {
        try {
            static::create([
                'event_name' => $eventName,
                'session_token' => session()->getId(),
                'booking_id' => $bookingId,
                'lead_id' => $leadId,
                'metadata' => $metadata ?: null,
            ]);
        } catch (\Throwable $e) {
            // Non-fatal — log and continue
            \Illuminate\Support\Facades\Log::channel('booking')->warning('FunnelEvent::fire failed', [
                'event' => $eventName,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
