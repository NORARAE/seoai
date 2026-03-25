<?php

namespace App\Filament\Resources\Sites\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SiteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Site Information')
                    ->schema([
                        TextEntry::make('name')
                            ->placeholder('—'),
                        TextEntry::make('domain')
                            ->copyable(),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('gsc_property_url')
                            ->label('GSC Property')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Sitemap')
                    ->schema([
                        TextEntry::make('sitemap_enabled')
                            ->label('Sitemap Enabled')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Enabled' : 'Disabled')
                            ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                        TextEntry::make('sitemap_include_payload_pages')
                            ->label('Include Payload Pages')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                            ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                        TextEntry::make('sitemap_include_discovered_pages')
                            ->label('Include Discovered Pages')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                            ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                        TextEntry::make('sitemap_max_urls_per_file')
                            ->label('Max URLs Per File'),
                        TextEntry::make('sitemap_index_url')
                            ->label('Sitemap URL')
                            ->copyable()
                            ->url(fn (?string $state) => $state, shouldOpenInNewTab: true)
                            ->columnSpanFull(),
                        TextEntry::make('gsc_last_sitemap_submission_status')
                            ->label('Last Sitemap Submission')
                            ->badge()
                            ->placeholder('—'),
                        TextEntry::make('gsc_last_sitemap_submission_at')
                            ->label('Submitted At')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('gsc_last_sitemap_submission_error')
                            ->label('Submission Error')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
