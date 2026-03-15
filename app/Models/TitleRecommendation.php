<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TitleRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'recommendable_type',
        'recommendable_id',
        'current_title',
        'suggested_title',
        'reasoning',
        'confidence_score',
        'status',
        'current_performance',
        'predicted_impact',
        'actual_impact',
        'generation_method',
        'generation_metadata',
        'generated_at',
        'reviewed_at',
        'applied_at',
        'measurement_completed_at',
        'reviewed_by',
        'baseline_snapshot_id',
    ];

    protected $casts = [
        'confidence_score' => 'decimal:2',
        'current_performance' => 'array',
        'predicted_impact' => 'array',
        'actual_impact' => 'array',
        'generation_metadata' => 'array',
        'generated_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'applied_at' => 'datetime',
        'measurement_completed_at' => 'datetime',
    ];

    /**
     * Get the site that owns the recommendation
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the page (polymorphic)
     */
    public function recommendable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who reviewed this recommendation
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the baseline snapshot
     */
    public function baselineSnapshot(): BelongsTo
    {
        return $this->belongsTo(BaselineSnapshot::class);
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeApplied($query)
    {
        return $query->where('status', 'applied');
    }

    public function scopeHighConfidence($query, $threshold = 70)
    {
        return $query->where('confidence_score', '>=', $threshold);
    }
}
