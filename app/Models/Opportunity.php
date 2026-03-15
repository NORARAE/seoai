<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Opportunity extends Model
{
    protected $fillable = [
        'site_id',
        'opportunifiable_type',
        'opportunifiable_id',
        'type', // Renamed from issue_type
        'priority_score',
        'score',
        'status',
        'recommendation',
        'description',
        'metrics',
        'addressed_by',
        'addressed_at',
        'resolution_notes',
        'optimization_run_id',
    ];

    protected $casts = [
        'priority_score' => 'integer',
        'score' => 'decimal:2',
        'metrics' => 'array',
        'addressed_at' => 'datetime',
    ];

    /**
     * Get the site
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the associated page (polymorphic)
     */
    public function opportunifiable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who addressed this opportunity
     */
    public function addressedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'addressed_by');
    }

    /**
     * Get the optimization run (if addressed)
     */
    public function optimizationRun(): BelongsTo
    {
        return $this->belongsTo(OptimizationRun::class);
    }

    /**
     * Scopes
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeHighPriority($query, int $threshold = 70)
    {
        return $query->where('score', '>=', $threshold);
    }

    /**
     * Mark opportunity as addressed
     */
    public function markAddressed(User $user, ?string $notes = null, ?OptimizationRun $run = null): bool
    {
        return $this->update([
            'status' => 'closed',
            'addressed_by' => $user->id,
            'addressed_at' => now(),
            'resolution_notes' => $notes,
            'optimization_run_id' => $run?->id,
        ]);
    }
}
