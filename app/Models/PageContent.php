<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageContent extends Model
{
    protected $table = 'page_content';

    protected $fillable = [
        'url_id',
        'body_text',
        'excerpt',
        'word_count',
        'readability',
    ];

    protected $casts = [
        'word_count' => 'integer',
        'readability' => 'decimal:2',
    ];

    public function url(): BelongsTo
    {
        return $this->belongsTo(UrlInventory::class, 'url_id');
    }
}
