<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'client_id',
        'job_name',
        'job_class',
        'status',
        'started_at',
        'completed_at',
        'duration_seconds',
        'items_processed',
        'items_succeeded',
        'items_failed',
        'error_message',
        'error_context',
        'summary',
        'metadata',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'error_context' => 'array',
        'summary' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the site
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeForJob($query, string $jobName)
    {
        return $query->where('job_name', $jobName);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('started_at', '>=', now()->subDays($days));
    }

    /**
     * Mark as completed
     */
    public function markCompleted(array $summary = []): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'duration_seconds' => now()->diffInSeconds($this->started_at),
            'summary' => $summary,
        ]);
    }

    /**
     * Mark as failed
     */
    public function markFailed(string $error, ?array $context = null): void
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'duration_seconds' => now()->diffInSeconds($this->started_at),
            'error_message' => $error,
            'error_context' => $context,
        ]);
    }
}
