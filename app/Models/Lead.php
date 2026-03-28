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
        'onboarding_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [];
    }

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
     * Called from BookingController after free store() and paid handlePaymentReturn().
     */
    public static function syncFromBooking(Booking $booking, ?string $paymentStatus = null): static
    {
        $booking->loadMissing('consultType');

        return static::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'name'           => $booking->name,
                'email'          => $booking->email,
                'company'        => $booking->company,
                'website'        => $booking->website,
                'phone'          => $booking->phone,
                'session_type'   => $booking->consultType?->name,
                'payment_status' => $paymentStatus,
                'source'         => 'booking',
            ]
        );
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
}
