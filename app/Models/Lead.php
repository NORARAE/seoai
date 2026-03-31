<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    protected $fillable = [
        'booking_id',
        'name',
        'email',
        'company',
        'website',
        'phone',
        'session_type',
        'payment_status',
        'source',
        'lifecycle_stage',
        'onboarding_status',
        'notes',
        'score',
        'grade',
        'scored_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'scored_at' => 'datetime',
        ];
    }

    // ── Lifecycle stage constants ─────────────────────────────────────────────
    public const STAGE_NEW = 'new';
    public const STAGE_BOOKED = 'booked';
    public const STAGE_PAID = 'paid';
    public const STAGE_ONBOARDING_SUBMITTED = 'onboarding_submitted';
    public const STAGE_APPROVED = 'approved';
    public const STAGE_ACTIVE = 'active';
    public const STAGE_REJECTED = 'rejected';
    public const STAGE_LOST = 'lost';

    // ── Relationships ──────────────────────────────────────────────────────────

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function onboardingSubmission(): HasOne
    {
        return $this->hasOne(OnboardingSubmission::class);
    }

    // ── Factory method ────────────────────────────────────────────────────────

    /**
     * Create or update the CRM lead record for a confirmed booking.
     *
     * Called from:
     *   BookingController::store()               — free bookings  (stage: booked)
     *   BookingController::handlePaymentReturn()  — paid bookings  (stage: paid)
     *   BookingWebhookController::handle()        — async webhook  (stage: paid)
     *
     * @param  string  $lifecycleStage  One of Lead::STAGE_* constants
     */
    public static function syncFromBooking(
        Booking $booking,
        ?string $paymentStatus = null,
        string $lifecycleStage = self::STAGE_BOOKED
    ): static {
        $booking->loadMissing('consultType');

        $lead = static::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'name' => $booking->name,
                'email' => $booking->email,
                'company' => $booking->company,
                'website' => $booking->website,
                'phone' => $booking->phone,
                'session_type' => $booking->consultType?->name,
                'payment_status' => $paymentStatus,
                'source' => 'booking',
                'lifecycle_stage' => $lifecycleStage,
            ]
        );

        // Compute/refresh lead score after every sync
        try {
            app(\App\Services\LeadScoringService::class)->score($lead);
        } catch (\Throwable) {
            // Non-fatal — scoring should never block booking creation
        }

        return $lead->refresh();
    }

    /**
     * Advance the lifecycle stage without touching onboarding_status.
     * Idempotent – safe to call multiple times with the same stage.
     */
    public function advanceLifecycle(string $stage): static
    {
        $this->update(['lifecycle_stage' => $stage]);

        return $this;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isPendingOnboarding(): bool
    {
        return $this->onboarding_status === 'pending';
    }

    public function isOnboardingSubmitted(): bool
    {
        return $this->onboarding_status === 'submitted';
    }

    public function isApproved(): bool
    {
        return $this->onboarding_status === 'approved';
    }

    public function isActive(): bool
    {
        return $this->lifecycle_stage === self::STAGE_ACTIVE;
    }

    public function lifecycleBadgeColor(): string
    {
        return match ($this->lifecycle_stage) {
            self::STAGE_ACTIVE => 'success',
            self::STAGE_APPROVED => 'success',
            self::STAGE_ONBOARDING_SUBMITTED => 'info',
            self::STAGE_PAID => 'warning',
            self::STAGE_BOOKED => 'gray',
            self::STAGE_REJECTED => 'danger',
            self::STAGE_LOST => 'danger',
            default => 'gray',
        };
    }
}
