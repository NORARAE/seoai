<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PagePayload extends Model
{
    protected $fillable = [
        'batch_id', 'site_id', 'client_id', 'service_id', 'location_id',
        'location_type', 'title', 'meta_description', 'slug',
        'canonical_url_suggestion', 'body_content', 'excerpt',
        'schema_json_ld', 'structured_data_type', 'og_image_url', 'og_tags',
        'internal_link_suggestions', 'anchor_text_suggestions', 'outbound_links',
        'parent_page_slug', 'hub_page_slug', 'related_pages', 'submenu_suggestions',
        'sitemap_priority', 'sitemap_changefreq', 'sitemap_lastmod',
        'publish_notes', 'reviewed_by_user_id', 'reviewed_at', 'review_notes',
        'publish_status', 'published_at', 'remote_id',
        'remote_url', 'remote_edit_url', 'content_quality_score',
        'seo_score', 'readability_score', 'generated_by', 'generation_params',
        'template_used', 'ai_model_used', 'status',
    ];

    protected $casts = [
        'schema_json_ld' => 'array',
        'og_tags' => 'array',
        'internal_link_suggestions' => 'array',
        'anchor_text_suggestions' => 'array',
        'outbound_links' => 'array',
        'related_pages' => 'array',
        'submenu_suggestions' => 'array',
        'generation_params' => 'array',
        'reviewed_at' => 'datetime',
        'published_at' => 'datetime',
        'sitemap_lastmod' => 'datetime',
        'content_quality_score' => 'decimal:2',
        'seo_score' => 'decimal:2',
        'readability_score' => 'decimal:2',
        'sitemap_priority' => 'decimal:2',
    ];

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(PageGenerationBatch::class, 'batch_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the location (City, County, or State)
     */
    public function location()
    {
        return match($this->location_type) {
            'city' => $this->belongsTo(City::class, 'location_id'),
            'county' => $this->belongsTo(County::class, 'location_id'),
            'state' => $this->belongsTo(State::class, 'location_id'),
            default => null,
        };
    }

    /**
     * Get the city if location_type is 'city'
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'location_id');
    }

    /**
     * Export as structured array for external publishing
     */
    public function toExportArray(): array
    {
        return [
            'meta' => [
                'generated_at' => $this->created_at->toIso8601String(),
                'payload_id' => $this->id,
                'site' => $this->site->domain,
            ],
            'content' => [
                'title' => $this->title,
                'meta_description' => $this->meta_description,
                'slug' => $this->slug,
                'body_html' => $this->body_content,
                'excerpt' => $this->excerpt,
            ],
            'seo' => [
                'schema_json_ld' => $this->schema_json_ld,
                'canonical_url' => $this->canonical_url_suggestion,
                'og_tags' => $this->og_tags,
            ],
            'hierarchy' => [
                'parent_page' => $this->parent_page_slug,
                'hub_page' => $this->hub_page_slug,
                'related_pages' => $this->related_pages,
            ],
            'internal_linking' => [
                'outgoing_links' => $this->internal_link_suggestions,
                'suggested_anchors' => $this->anchor_text_suggestions,
            ],
            'navigation' => [
                'submenu_suggestions' => $this->submenu_suggestions,
            ],
            'sitemap' => [
                'priority' => (float) $this->sitemap_priority,
                'changefreq' => $this->sitemap_changefreq,
            ],
            'notes' => $this->publish_notes,
        ];
    }

    /**
     * Export payload in specified format
     * 
     * @param string $format json|markdown|html|csv
     * @return string
     */
    public function toExportFormat(string $format = 'json'): string
    {
        $data = $this->toExportArray();
        
        return match($format) {
            'json' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'markdown' => $this->toMarkdown($data),
            'html' => $this->toHtml($data),
            'csv' => $this->toCsv($data),
            default => json_encode($data),
        };
    }

    /**
     * Convert to Markdown format
     */
    protected function toMarkdown(array $data): string
    {
        $md = "# {$data['content']['title']}\n\n";
        
        if ($data['content']['meta_description']) {
            $md .= "**Description:** {$data['content']['meta_description']}\n\n";
        }
        
        $md .= "**Slug:** {$data['content']['slug']}\n\n";
        $md .= "---\n\n";
        $md .= $data['content']['body_html'] . "\n\n";
        
        if ($data['seo']['schema_json_ld']) {
            $md .= "## Schema\n\n```json\n";
            $md .= json_encode($data['seo']['schema_json_ld'], JSON_PRETTY_PRINT);
            $md .= "\n```\n\n";
        }
        
        return $md;
    }

    /**
     * Convert to HTML format
     */
    protected function toHtml(array $data): string
    {
        $html = "<!DOCTYPE html>\n<html>\n<head>\n";
        $html .= "<title>{$data['content']['title']}</title>\n";
        
        if ($data['content']['meta_description']) {
            $html .= "<meta name=\"description\" content=\"{$data['content']['meta_description']}\">\n";
        }
        
        $html .= "</head>\n<body>\n";
        $html .= "<h1>{$data['content']['title']}</h1>\n";
        $html .= $data['content']['body_html'];
        $html .= "\n</body>\n</html>";
        
        return $html;
    }

    /**
     * Convert to CSV format (simplified single-row representation)
     */
    protected function toCsv(array $data): string
    {
        $row = [
            $data['meta']['payload_id'],
            $data['content']['title'],
            $data['content']['slug'],
            $data['content']['meta_description'] ?? '',
            strip_tags($data['content']['body_html'] ?? ''),
        ];
        
        return implode(',', array_map(function($field) {
            return '"' . str_replace('"', '""', $field) . '"';
        }, $row));
    }

    /**
     * Mark as published with remote details
     */
    public function markAsPublished(string $remoteId, string $remoteUrl, ?string $editUrl = null): void
    {
        $this->update([
            'publish_status' => 'published',
            'published_at' => now(),
            'remote_id' => $remoteId,
            'remote_url' => $remoteUrl,
            'remote_edit_url' => $editUrl,
            'status' => 'published',
        ]);
    }

    /**
     * Mark as exported (for non-native CMS)
     */
    public function markAsExported(): void
    {
        $this->update([
            'publish_status' => 'exported',
            'status' => 'published',
        ]);
    }

    /**
     * Mark publishing as failed while preserving review context
     */
    public function markAsFailed(?string $reason = null): void
    {
        $attributes = [
            'publish_status' => 'failed',
            'status' => 'failed',
        ];

        if (filled($reason)) {
            $attributes['review_notes'] = $reason;
        }

        $this->update($attributes);
    }

    /**
     * Approve payload for publishing
     */
    public function approve(?int $userId = null, ?string $note = null): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by_user_id' => $userId,
            'reviewed_at' => now(),
            'review_notes' => $note,
        ]);
    }

    /**
     * Reject payload and store review feedback
     */
    public function reject(?int $userId = null, string $reason = ''): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by_user_id' => $userId,
            'reviewed_at' => now(),
            'review_notes' => $reason,
        ]);
    }

    public function isAwaitingReview(): bool
    {
        return $this->status === 'needs_review';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isEditable(): bool
    {
        return ! in_array($this->status, ['published'], true);
    }

    /**
     * Check if payload is ready to publish
     */
    public function isReadyToPublish(): bool
    {
        return $this->status === 'approved'
            && $this->publish_status === 'pending'
            && !empty($this->body_content)
            && !empty($this->title);
    }

    public function getSectionCountAttribute(): int
    {
        return count($this->preview_sections);
    }

    public function getBodyLengthAttribute(): int
    {
        return strlen((string) $this->body_content);
    }

    public function getFormattedBodyLengthAttribute(): string
    {
        if ($this->body_length < 1024) {
            return $this->body_length . ' B';
        }

        return number_format($this->body_length / 1024, 1) . ' KB';
    }

    public function getHasSchemaAttribute(): bool
    {
        return filled($this->schema_json_ld);
    }

    public function getInternalLinksCountAttribute(): int
    {
        return count($this->internal_link_suggestions ?? []);
    }

    public function getPreviewBodyHtmlAttribute(): string
    {
        $html = (string) $this->body_content;
        $html = preg_replace('#<script\b[^>]*>(.*?)</script>#is', '', $html) ?? $html;
        $html = preg_replace('#<style\b[^>]*>(.*?)</style>#is', '', $html) ?? $html;

        return $html;
    }

    public function getPreviewSectionsAttribute(): array
    {
        $html = (string) $this->preview_body_html;

        if (blank($html)) {
            return [];
        }

        preg_match_all('/<h2[^>]*>(.*?)<\/h2>(.*?)(?=<h2[^>]*>|$)/is', $html, $matches, PREG_SET_ORDER);

        return collect($matches)
            ->map(function (array $match): array {
                $heading = trim(strip_tags(html_entity_decode($match[1] ?? '', ENT_QUOTES | ENT_HTML5)));
                $content = trim($match[2] ?? '');

                return [
                    'heading' => filled($heading) ? $heading : 'Section',
                    'content' => $content,
                    'excerpt' => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($content))), 180),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Get location name for display
     */
    public function getLocationNameAttribute(): string
    {
        if ($this->location_type === 'city' && $this->city) {
            return $this->city->name . ', ' . $this->site->state->code;
        }

        if ($this->location_type === 'county' && $this->relationLoaded('location') && $this->location) {
            return $this->location->name;
        }

        if ($this->location_type === 'state' && $this->relationLoaded('location') && $this->location) {
            return $this->location->name;
        }
        
        return 'Unknown Location';
    }
}
