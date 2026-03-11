<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkSuggestion extends Model
{
    protected $fillable = [
        'site_id',
        'source_page_id',
        'target_page_id',
        'suggested_anchor_text',
        'reason',
        'status',
    ];

    /**
     * Get the site this suggestion belongs to
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the source page (where the link should be added)
     */
    public function sourcePage(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'source_page_id');
    }

    /**
     * Get the target page (where the link should point to)
     */
    public function targetPage(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'target_page_id');
    }
}
