<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BaselineSnapshot extends Model
{
    public const UPDATED_AT = null; // Snapshots are immutable after creation

    protected $fillable = [
        'snapshotable_type',
        'snapshotable_id',
        'site_id',
        'snapshot_date',
        'title',
        'meta_description',
        'h1',
        'canonical_url',
        'content_hash',
        'rendered_html_hash',
        'schema_json',
        'performance_snapshot_json',
    ];

    protected $casts = [
        'snapshot_date' => 'datetime',
        'schema_json' => 'array',
        'performance_snapshot_json' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the parent snapshotable model (Page or LocationPage)
     */
    public function snapshotable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the site this snapshot belongs to
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get optimization runs that reference this baseline
     */
    public function optimizationRuns(): HasMany
    {
        return $this->hasMany(OptimizationRun::class);
    }

    /**
     * Create a snapshot from a Page or LocationPage model
     */
    public static function createFromModel(Model $model, ?array $performanceData = null): self
    {
        $snapshot = new self();
        
        $snapshot->snapshotable_type = get_class($model);
        $snapshot->snapshotable_id = $model->id;
        $snapshot->site_id = $model->site_id ?? null;
        $snapshot->snapshot_date = now();
        
        // Capture page attributes
        $snapshot->title = $model->title ?? $model->meta_title ?? null;
        $snapshot->meta_description = $model->meta_description ?? null;
        $snapshot->h1 = $model->h1 ?? null;
        $snapshot->canonical_url = $model->canonical_url ?? $model->url ?? null;
        
        // Generate content hash
        if (method_exists($model, 'getContentForSnapshot')) {
            $snapshot->content_hash = hash('sha256', $model->getContentForSnapshot());
        } elseif (isset($model->body_sections_json)) {
            $snapshot->content_hash = hash('sha256', json_encode($model->body_sections_json));
        } elseif (isset($model->content)) {
            $snapshot->content_hash = hash('sha256', $model->content);
        }
        
        // For LocationPages with rendered HTML cache
        if (isset($model->rendered_html_cache)) {
            $snapshot->rendered_html_hash = hash('sha256', $model->rendered_html_cache);
        }
        
        // Schema snapshot
        if (isset($model->schema_cache_json)) {
            $snapshot->schema_json = $model->schema_cache_json;
        } elseif (isset($model->service_schema_json) || isset($model->local_business_schema_json)) {
            $snapshot->schema_json = [
                'service' => $model->service_schema_json ?? null,
                'local_business' => $model->local_business_schema_json ?? null,
                'faq' => $model->faq_schema_json ?? null,
            ];
        }
        
        // Performance snapshot if provided
        if ($performanceData) {
            $snapshot->performance_snapshot_json = $performanceData;
        }
        
        $snapshot->save();
        
        return $snapshot;
    }

    /**
     * Check if content has changed since this snapshot
     */
    public function hasContentChanged(Model $model): bool
    {
        $currentHash = null;
        
        if (method_exists($model, 'getContentForSnapshot')) {
            $currentHash = hash('sha256', $model->getContentForSnapshot());
        } elseif (isset($model->body_sections_json)) {
            $currentHash = hash('sha256', json_encode($model->body_sections_json));
        } elseif (isset($model->content)) {
            $currentHash = hash('sha256', $model->content);
        }
        
        return $currentHash !== null && $this->content_hash !== $currentHash;
    }
}
