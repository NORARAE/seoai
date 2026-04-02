<?php

namespace App\Contracts;

use App\Models\Booking;

/**
 * UPGRADE PATH STUB — Prepared for future implementation.
 *
 * This interface defines the contract for the audit → proposal → build upgrade path.
 * Full logic is NOT yet implemented (Section 8 of the funnel architecture request).
 *
 * Upgrade flow intended:
 *   1. Client completes Market Opportunity Audit ($500)
 *   2. Proposal is generated post-audit (manual or automated)
 *   3. Client is presented with upgrade options (Strategy Session / Full Build)
 *   4. Conversion is tracked via booking_type upgrade path
 */
interface ProposalUpgradePath
{
    /**
     * Generate a proposal outline from a completed audit booking.
     * Should summarise findings and present upgrade options.
     */
    public function generateFromAudit(Booking $auditBooking): array;

    /**
     * Determine the recommended next step (strategy session or full build)
     * based on audit notes / onboarding data.
     */
    public function recommendNextTier(Booking $auditBooking): string;

    /**
     * Record that a client accepted an upgrade offer
     * (audit → strategy or audit → build).
     */
    public function recordUpgrade(Booking $originalBooking, string $newBookingType): void;
}
