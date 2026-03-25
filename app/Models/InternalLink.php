<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternalLink extends Model
{
    protected $fillable = [
        'site_id',
        'source_page_id',
        'source_url_id',
        'target_url_id',
        'source_url',
        'target_url',
        'anchor_text',
    ];

    /**
     * Get the site this link belongs to
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the source page (where the link appears)
     */
    public function sourcePage(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'source_page_id');
    }

    public function sourceUrlInventory(): BelongsTo
    {
        return $this->belongsTo(UrlInventory::class, 'source_url_id');
    }

    public function targetUrlInventory(): BelongsTo
    {
        return $this->belongsTo(UrlInventory::class, 'target_url_id');
    }
}
