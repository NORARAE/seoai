<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LocationPage extends Model
{
    protected $fillable = [
        'type',
        'state_id',
        'county_id',
        'city_id',
        'service_id',
        'parent_location_page_id',
        'slug',
        'url_path',
        'title',
        'meta_title',
        'meta_description',
        'h1',
        'canonical_url',
        'body_sections_json',
        'internal_links_json',
        'score',
        'status',
        'is_indexable',
        'generated_at',
        'needs_review',
        'review_notes',
        'content_quality_status',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'body_sections_json' => 'array',
        'internal_links_json' => 'array',
        'score' => 'integer',
        'is_indexable' => 'boolean',
        'generated_at' => 'datetime',
        'needs_review' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the state this page belongs to
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the county this page belongs to
     */
    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    /**
     * Get the city this page belongs to (if service_city type)
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the service this page belongs to (if service_city type)
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the parent location page (county hub for service_city pages)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(LocationPage::class, 'parent_location_page_id');
    }

    /**
     * Get child location pages (service_city pages under a county hub)
     */
    public function children(): HasMany
    {
        return $this->hasMany(LocationPage::class, 'parent_location_page_id');
    }

    /**
     * Get the user who approved this page
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope to filter county hub pages
     */
    public function scopeCountyHub($query)
    {
        return $query->where('type', 'county_hub');
    }

    /**
     * Scope to filter service-city pages
     */
    public function scopeServiceCity($query)
    {
        return $query->where('type', 'service_city');
    }

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter published pages
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to filter draft pages
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope to filter pages needing review
     */
    public function scopeNeedsReview($query)
    {
        return $query->where('needs_review', true);
    }

    /**
     * Scope to filter approved pages
     */
    public function scopeApproved($query)
    {
        return $query->where('content_quality_status', 'approved');
    }

    /**
     * Scope to filter excluded pages
     */
    public function scopeExcluded($query)
    {
        return $query->where('content_quality_status', 'excluded');
    }

    /**
     * Scope to filter unreviewed pages
     */
    public function scopeUnreviewed($query)
    {
        return $query->where('content_quality_status', 'unreviewed');
    }

    /**
     * Scope to filter edited pages
     */
    public function scopeEdited($query)
    {
        return $query->where('content_quality_status', 'edited');
    }
}
