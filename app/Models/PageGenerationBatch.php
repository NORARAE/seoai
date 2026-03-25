<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageGenerationBatch extends Model
{
    protected $fillable = [
        'site_id',
        'client_id',
        'initiated_by_user_id',
        'name',
        'description',
        'opportunity_source',
        'auto_publish',
        'requested_count',
        'successful_count',
        'payload_count',
        'published_count',
        'exported_count',
        'failed_count',
        'error_summary',
        'failed_items',
        'export_path',
        'export_format',
        'duration_seconds',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'auto_publish' => 'boolean',
        'failed_items' => 'array',
        'duration_seconds' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by_user_id');
    }

    public function payloads(): HasMany
    {
        return $this->hasMany(PagePayload::class, 'batch_id');
    }

    /**
     * Get progress percentage for payload generation
     */
    public function getPayloadProgressPercentage(): float
    {
        if ($this->requested_count === 0) {
            return 0;
        }
        return ($this->payload_count / $this->requested_count) * 100;
    }

    /**
     * Get progress percentage for publishing
     */
    public function getPublishingProgressPercentage(): float
    {
        if ($this->payload_count === 0) {
            return 0;
        }
        return (($this->published_count + $this->exported_count) / $this->payload_count) * 100;
    }

    /**
     * Check if batch is complete
     */
    public function isComplete(): bool
    {
        return in_array($this->status, ['completed', 'failed', 'cancelled']);
    }

    /**
     * Mark batch as processing
     */
    public function markAsProcessing(): void
    {
        $this->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);
    }

    /**
     * Mark batch as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark batch as failed
     */
    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'error_summary' => ['reason' => $reason],
        ]);
    }

    /**
     * Increment payload count
     */
    public function incrementPayload(): void
    {
        $this->increment('payload_count');
    }

    /**
     * Increment successful generation count
     */
    public function incrementSuccessful(): void
    {
        $this->increment('successful_count');
    }

    /**
     * Increment published count
     */
    public function incrementPublished(): void
    {
        $this->increment('published_count');
    }

    /**
     * Increment exported count
     */
    public function incrementExported(): void
    {
        $this->increment('exported_count');
    }

    /**
     * Determine whether all queued generation work has finished
     */
    public function hasFinishedGeneration(): bool
    {
        return ($this->payload_count + $this->failed_count) >= $this->requested_count;
    }

    /**
     * Reconcile the persisted batch status with current counters
     */
    public function reconcileGenerationStatus(): void
    {
        $this->refresh();

        if ($this->status !== 'processing') {
            return;
        }

        if (! $this->hasFinishedGeneration()) {
            return;
        }

        $status = ($this->payload_count === 0 && $this->failed_count > 0) ? 'failed' : 'completed';

        $this->update([
            'status' => $status,
            'completed_at' => now(),
            'duration_seconds' => $this->started_at ? now()->diffInSeconds($this->started_at) : null,
        ]);
    }

    /**
     * Get summary statistics
     */
    public function getStats(): array
    {
        return [
            'requested' => $this->requested_count,
            'successful' => $this->successful_count,
            'payloads_generated' => $this->payload_count,
            'published' => $this->published_count,
            'exported' => $this->exported_count,
            'failed' => $this->failed_count,
            'payload_progress' => $this->getPayloadProgressPercentage(),
            'publishing_progress' => $this->getPublishingProgressPercentage(),
        ];
    }
}
