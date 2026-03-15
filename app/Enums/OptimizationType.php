<?php

namespace App\Enums;

enum OptimizationType: string
{
    case TITLE = 'title';
    case META_DESCRIPTION = 'meta_description';
    case CONTENT = 'content';
    case SCHEMA = 'schema';
    case LINKS = 'links';
    case OTHER = 'other';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match($this) {
            self::TITLE => 'Title Optimization',
            self::META_DESCRIPTION => 'Meta Description',
            self::CONTENT => 'Content Update',
            self::SCHEMA => 'Schema Enhancement',
            self::LINKS => 'Link Optimization',
            self::OTHER => 'Other',
        };
    }

    /**
     * Get all values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get options for select dropdown
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
