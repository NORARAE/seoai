<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseValidation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'license_key',
        'site_url',
        'plugin_ver',
        'result',
        'ip_address',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class, 'license_key', 'license_key');
    }
}