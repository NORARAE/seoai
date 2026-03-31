<?php

namespace App\Services;

use App\Models\Lead;

/**
 * Rules-based lead scoring engine.
 *
 * Scores are additive. Maximum possible score is 100.
 * Grade thresholds: A ≥ 80, B ≥ 60, C ≥ 40, D < 40.
 *
 * All scoring logic lives here — never in the model or controllers — so it can
 * be tuned or replaced without touching the rest of the system.
 */
class LeadScoringService
{
    // ── Grade cutoffs ─────────────────────────────────────────────────────────

    private const GRADE_A = 80;
    private const GRADE_B = 60;
    private const GRADE_C = 40;

    // ── Paid session bonus ────────────────────────────────────────────────────

    /**
     * Point weights applied per signal.
     * Change these values to tune scoring without touching logic.
     */
    private const WEIGHTS = [
        'paid_booking' => 30,  // booked a paid session
        'has_website' => 15,  // submitted a website URL
        'has_company' => 10,  // provided company name
        'has_phone' => 10,  // provided phone number
        'has_message' => 10,  // wrote a custom message
        'lifecycle_advanced' => 10,  // stage is ≥ paid
        'onboarding_submitted' => 10,  // completed onboarding form
        'onboarding_approved' => 5,   // admin approved onboarding
    ];

    /**
     * Compute and persist the score for a lead.
     * Safe to call multiple times — always overwrites previous score.
     */
    public function score(Lead $lead): Lead
    {
        $points = 0;
        $lead->loadMissing('booking');

        $booking = $lead->booking;

        // Paid session
        if ($booking && !$booking->consultType?->is_free) {
            $points += self::WEIGHTS['paid_booking'];
        }

        // Website provided
        if (!empty($lead->website)) {
            $points += self::WEIGHTS['has_website'];
        }

        // Company provided
        if (!empty($lead->company)) {
            $points += self::WEIGHTS['has_company'];
        }

        // Phone provided
        if (!empty($lead->phone)) {
            $points += self::WEIGHTS['has_phone'];
        }

        // Custom message provided
        if ($booking && !empty($booking->message)) {
            $points += self::WEIGHTS['has_message'];
        }

        // Lifecycle advanced beyond booked
        if (
            in_array($lead->lifecycle_stage, [
                Lead::STAGE_PAID,
                Lead::STAGE_ONBOARDING_SUBMITTED,
                Lead::STAGE_APPROVED,
                Lead::STAGE_ACTIVE,
            ])
        ) {
            $points += self::WEIGHTS['lifecycle_advanced'];
        }

        // Onboarding submitted
        if ($lead->onboarding_status === 'submitted') {
            $points += self::WEIGHTS['onboarding_submitted'];
        }

        // Onboarding approved
        if ($lead->onboarding_status === 'approved') {
            $points += self::WEIGHTS['onboarding_approved'];
        }

        $score = min(100, $points);
        $grade = $this->grade($score);

        $lead->update([
            'score' => $score,
            'grade' => $grade,
            'scored_at' => now(),
        ]);

        return $lead->refresh();
    }

    /**
     * Derive an A–D letter grade from a numeric score.
     */
    public function grade(int $score): string
    {
        return match (true) {
            $score >= self::GRADE_A => 'A',
            $score >= self::GRADE_B => 'B',
            $score >= self::GRADE_C => 'C',
            default => 'D',
        };
    }
}
