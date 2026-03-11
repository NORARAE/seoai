<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'status',
        'notes',
    ];

    /**
     * Get all sites for this client
     */
    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }
}
