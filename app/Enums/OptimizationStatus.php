<?php

namespace App\Enums;

enum OptimizationStatus: string
{
    case DETECTED = 'detected';
    case RECOMMENDED = 'recommended';
    case APPROVED = 'approved';
    case APPLIED = 'applied';
    case MONITORING = 'monitoring';
    case SUCCEEDED = 'succeeded';
    case FAILED = 'failed';
    case ROLLED_BACK = 'rolled_back';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match($this) {
            self::DETECTED => 'Detected',
            self::RECOMMENDED => 'Recommended',
            self::APPROVED => 'Approved',
            self::APPLIED => 'Applied',
            self::MONITORING => 'Monitoring',
            self::SUCCEEDED => 'Succeeded',
            self::FAILED => 'Failed',
            self::ROLLED_BACK => 'Rolled Back',
        };
    }

    /**
     * Get color for UI display
     */
    public function color(): string
    {
        return match($this) {
            self::DETECTED => 'gray',
            self::RECOMMENDED => 'info',
            self::APPROVED => 'primary',
            self::APPLIED => 'warning',
            self::MONITORING => 'warning',
            self::SUCCEEDED => 'success',
            self::FAILED => 'danger',
            self::ROLLED_BACK => 'danger',
        };
    }

    /**
     * Get all values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get options for select dropdown
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }

    /**
     * Check if this is a terminal status
     */
    public function isTerminal(): bool
    {
        return in_array($this, [self::SUCCEEDED, self::FAILED, self::ROLLED_BACK]);
    }

    /**
     * Check if this status requires monitoring
     */
    public function needsMonitoring(): bool
    {
        return $this === self::MONITORING;
    }
}
