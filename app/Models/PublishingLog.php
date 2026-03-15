<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublishingLog extends Model
{
    protected $fillable = [
        'payload_id',
        'site_id',
        'adapter_type',
        'action',
        'result',
        'error_message',
        'remote_response',
        'request_data',
        'remote_id',
        'remote_url',
    ];

    protected $casts = [
        'remote_response' => 'array',
        'request_data' => 'array',
    ];

    public function payload(): BelongsTo
    {
        return $this->belongsTo(PagePayload::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
