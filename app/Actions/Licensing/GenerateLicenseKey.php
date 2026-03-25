<?php

namespace App\Actions\Licensing;

use App\Models\License;
use Illuminate\Support\Str;

class GenerateLicenseKey
{
    public function __invoke(): string
    {
        do {
            $uuid = strtoupper(str_replace('-', '', (string) Str::uuid()));
            $licenseKey = implode('-', str_split(substr($uuid, 0, 16), 4));
        } while (License::query()->where('license_key', $licenseKey)->exists());

        return $licenseKey;
    }
}