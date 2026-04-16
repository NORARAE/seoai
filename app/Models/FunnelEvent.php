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
        'user_id',
        'scan_id',
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

    // Homepage
    public const HOMEPAGE_CTA_CLICK = 'homepage_cta_click';

    // Scan funnel
    public const SCAN_STARTED = 'scan_started';
    public const SCAN_COMPLETED = 'scan_completed';

    // Upgrade funnel
    public const UPGRADE_CLICK = 'upgrade_click';
    public const UPGRADE_PURCHASED = 'upgrade_purchased';

    // High-ticket / deployment
    public const DEPLOYMENT_CTA_CLICK = 'deployment_cta_click';

    // Onboarding
    public const ONBOARDING_STARTED = 'onboarding_started';
    public const ONBOARDING_COMPLETED = 'onboarding_completed';

    // Booking (existing)
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function scan(): BelongsTo
    {
        return $this->belongsTo(QuickScan::class, 'scan_id');
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
        array $metadata = [],
        ?int $userId = null,
        ?int $scanId = null,
    ): void {
        try {
            static::create([
                'event_name' => $eventName,
                'session_token' => session()->getId(),
                'user_id' => $userId ?? auth()->id(),
                'scan_id' => $scanId,
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
