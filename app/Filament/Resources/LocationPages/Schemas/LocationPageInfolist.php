<?php

namespace App\Filament\Resources\LocationPages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LocationPageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('type')
                    ->label('Page Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'county_hub' => 'info',
                        'service_city' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'county_hub' => 'County Hub',
                        'service_city' => 'Service-City',
                        default => $state,
                    }),

                TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'warning',
                        'published' => 'success',
                        'archived' => 'gray',
                        default => 'gray',
                    }),

                TextEntry::make('content_quality_status')
                    ->label('Quality Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unreviewed' => 'gray',
                        'edited' => 'warning',
                        'approved' => 'success',
                        'excluded' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextEntry::make('needs_review')
                    ->label('Needs Review')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'warning' : 'success')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),

                TextEntry::make('review_notes')
                    ->label('Review Notes')
                    ->placeholder('—')
                    ->columnSpanFull(),

                TextEntry::make('approved_at')
                    ->label('Approved At')
                    ->dateTime()
                    ->placeholder('—'),

                TextEntry::make('approvedBy.name')
                    ->label('Approved By')
                    ->placeholder('—'),

                TextEntry::make('is_indexable')
                    ->label('Indexable')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),

                TextEntry::make('score')
                    ->label('Quality Score')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === null => 'gray',
                        $state >= 70 => 'success',
                        $state >= 50 => 'info',
                        default => 'warning',
                    })
                    ->placeholder('N/A'),

                TextEntry::make('state.name')
                    ->label('State'),

                TextEntry::make('county.name')
                    ->label('County'),

                TextEntry::make('city.name')
                    ->label('City')
                    ->placeholder('—'),

                TextEntry::make('service.name')
                    ->label('Service')
                    ->placeholder('—'),

                TextEntry::make('parent.title')
                    ->label('Parent Page')
                    ->placeholder('—'),

                TextEntry::make('slug')
                    ->label('Slug')
                    ->copyable(),

                TextEntry::make('url_path')
                    ->label('URL Path')
                    ->copyable(),

                TextEntry::make('canonical_url')
                    ->label('Canonical URL')
                    ->copyable()
                    ->url(fn ($state) => $state, shouldOpenInNewTab: true),

                TextEntry::make('title')
                    ->label('Title')
                    ->columnSpanFull(),

                TextEntry::make('meta_title')
                    ->label('Meta Title')
                    ->columnSpanFull(),

                TextEntry::make('meta_description')
                    ->label('Meta Description')
                    ->columnSpanFull(),

                TextEntry::make('h1')
                    ->label('H1 Heading')
                    ->columnSpanFull(),

                TextEntry::make('body_sections_json')
                    ->label('Body Content Structure')
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '—')
                    ->columnSpanFull(),

                TextEntry::make('generated_at')
                    ->label('Generated At')
                    ->dateTime(),

                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime(),

                TextEntry::make('updated_at')
                    ->label('Updated At')
                    ->dateTime(),
            ]);
    }
}
