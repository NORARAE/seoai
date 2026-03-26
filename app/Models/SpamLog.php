<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpamLog extends Model
{
    protected $fillable = [
        'inquiry_id',
        'reason',
        'spam_risk',
        'risk_score',
        'ip_address',
        'email',
        'signals',
    ];

    protected function casts(): array
    {
        return [
            'signals'    => 'array',
            'risk_score' => 'float',
        ];
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }
}
