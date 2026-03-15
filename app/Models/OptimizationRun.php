<?php

namespace App\Models;

use App\Enums\OptimizationStatus;
use App\Enums\OptimizationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OptimizationRun extends Model
{
    protected $fillable = [
        'site_id',
        'optimizable_type',
        'optimizable_id',
        'optimization_type',
        'status',
        'confidence_score',
        'auto_applied',
        'approved_by',
        'approved_at',
        'baseline_snapshot_id',
        'before_state_json',
        'proposed_state_json',
        'applied_state_json',
        'predicted_impact_json',
        'actual_impact_json',
        'monitoring_started_at',
        'monitoring_ends_at',
        'success_criteria_json',
        'rollback_reason',
        'rolled_back_at',
    ];

    protected $casts = [
        'optimization_type' => OptimizationType::class,
        'status' => OptimizationStatus::class,
        'confidence_score' => 'integer',
        'auto_applied' => 'boolean',
        'approved_at' => 'datetime',
        'before_state_json' => 'array',
        'proposed_state_json' => 'array',
        'applied_state_json' => 'array',
        'predicted_impact_json' => 'array',
        'actual_impact_json' => 'array',
        'monitoring_started_at' => 'datetime',
        'monitoring_ends_at' => 'datetime',
        'success_criteria_json' => 'array',
        'rolled_back_at' => 'datetime',
    ];

    /**
     * Get the parent optimizable model (Page or LocationPage)
     */
    public function optimizable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the site this optimization belongs to
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the user who approved this optimization
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the baseline snapshot for this optimization
     */
    public function baselineSnapshot(): BelongsTo
    {
        return $this->belongsTo(BaselineSnapshot::class);
    }

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, OptimizationStatus|string $status)
    {
        if (is_string($status)) {
            $status = OptimizationStatus::from($status);
        }
        
        return $query->where('status', $status);
    }

    /**
     * Scope to find runs that need monitoring
     */
    public function scopeNeedsMonitoring($query)
    {
        return $query->where('status', OptimizationStatus::MONITORING)
            ->where('monitoring_ends_at', '<=', now());
    }

    /**
     * Scope to filter auto-applied runs
     */
    public function scopeAutoApplied($query)
    {
        return $query->where('auto_applied', true);
    }

    /**
     * Mark this optimization as successful
     */
    public function markAsSucceeded(array $actualImpact = []): void
    {
        $this->update([
            'status' => OptimizationStatus::SUCCEEDED,
            'actual_impact_json' => $actualImpact,
        ]);
    }

    /**
     * Mark this optimization as failed
     */
    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'status' => OptimizationStatus::FAILED,
            'rollback_reason' => $reason,
        ]);
    }

    /**
     * Roll back this optimization
     */
    public function rollback(string $reason): void
    {
        $this->update([
            'status' => OptimizationStatus::ROLLED_BACK,
            'rollback_reason' => $reason,
            'rolled_back_at' => now(),
        ]);
    }

    /**
     * Check if this optimization is in monitoring phase
     */
    public function isMonitoring(): bool
    {
        return $this->status === OptimizationStatus::MONITORING;
    }

    /**
     * Check if monitoring period has ended
     */
    public function hasMonitoringEnded(): bool
    {
        return $this->monitoring_ends_at && $this->monitoring_ends_at->isPast();
    }
}
