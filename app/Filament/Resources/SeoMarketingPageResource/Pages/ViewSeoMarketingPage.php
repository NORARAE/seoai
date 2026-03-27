<?php

namespace App\Filament\Resources\SeoMarketingPageResource\Pages;

use App\Filament\Resources\SeoMarketingPageResource;
use App\Models\SeoMarketingPage;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ViewSeoMarketingPage extends ViewRecord
{
    protected static string $resource = SeoMarketingPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_public')
                ->label('View Live Page')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('gray')
                ->url(fn (): string => url('/' . $this->record->url_slug))
                ->openUrlInNewTab(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('SEO Metadata')
                ->columns(2)
                ->schema([
                    TextEntry::make('meta_title')
                        ->label('Meta Title')
                        ->columnSpanFull()
                        ->placeholder('—'),

                    TextEntry::make('meta_description')
                        ->label('Meta Description')
                        ->columnSpanFull()
                        ->placeholder('—'),

                    TextEntry::make('url_slug')
                        ->label('Canonical URL')
                        ->formatStateUsing(fn (SeoMarketingPage $record): string => url('/' . $record->url_slug))
                        ->url(fn (SeoMarketingPage $record): string => url('/' . $record->url_slug))
                        ->openUrlInNewTab()
                        ->fontFamily('mono')
                        ->color('primary'),

                    TextEntry::make('cluster')
                        ->label('Cluster')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'core'     => 'primary',
                            'agency'   => 'info',
                            'local'    => 'success',
                            'strategy' => 'warning',
                            'industry' => 'danger',
                            default    => 'gray',
                        }),

                    TextEntry::make('search_intent')
                        ->label('Search Intent')
                        ->badge()
                        ->color('gray'),

                    TextEntry::make('money_page_rank')
                        ->label('Money Page Rank')
                        ->placeholder('—')
                        ->badge()
                        ->color('warning'),

                    TextEntry::make('sitemap_priority')
                        ->label('Sitemap Priority')
                        ->formatStateUsing(fn ($state) => number_format((float) $state, 2)),

                    TextEntry::make('sitemap_changefreq')
                        ->label('Change Frequency')
                        ->badge()
                        ->color('gray'),
                ]),

            Section::make('Content')
                ->schema([
                    TextEntry::make('h1')
                        ->label('H1 Headline')
                        ->columnSpanFull()
                        ->weight('semibold'),

                    TextEntry::make('h2_structure')
                        ->label('H2 Sections')
                        ->columnSpanFull()
                        ->listWithLineBreaks()
                        ->formatStateUsing(fn ($state) => is_array($state)
                            ? implode("\n", $state)
                            : ($state ?? '—')),

                    TextEntry::make('hook')
                        ->label('Hook / Introduction')
                        ->columnSpanFull()
                        ->prose()
                        ->placeholder('—'),
                ]),

            Section::make('Keywords')
                ->columns(2)
                ->schema([
                    TextEntry::make('primary_keyword')
                        ->label('Primary Keyword')
                        ->placeholder('—'),

                    TextEntry::make('secondary_keywords')
                        ->label('Secondary Keywords')
                        ->columnSpanFull()
                        ->formatStateUsing(fn ($state): string => is_array($state)
                            ? implode(', ', $state)
                            : ($state ?? '—')),
                ]),

            Section::make('Internal Links & Homepage Link Strategy')
                ->schema([
                    TextEntry::make('internal_links')
                        ->label('Lateral Links')
                        ->columnSpanFull()
                        ->formatStateUsing(function ($state, SeoMarketingPage $record): string {
                            $lateral = $record->lateral_links;
                            if (empty($lateral)) {
                                return '—';
                            }
                            return implode("\n", array_map(
                                fn ($l) => ($l['anchor'] ?? '?') . '  →  ' . ($l['url'] ?? ''),
                                $lateral
                            ));
                        })
                        ->fontFamily('mono'),

                    TextEntry::make('homepage_ctas')
                        ->label('Homepage CTA Strategy')
                        ->columnSpanFull()
                        ->getStateUsing(function (SeoMarketingPage $record): string {
                            $ctas = $record->homepage_ctas;
                            if (empty($ctas)) {
                                return '—';
                            }
                            return implode("\n", array_map(
                                fn ($c) => '[' . strtoupper($c['position'] ?? '?') . ']  ' . ($c['anchor'] ?? '') . '  →  ' . ($c['url'] ?? ''),
                                $ctas
                            ));
                        })
                        ->fontFamily('mono'),
                ]),

            Section::make('Schema (JSON-LD)')
                ->schema([
                    TextEntry::make('schema_json')
                        ->label('Stored Schema')
                        ->columnSpanFull()
                        ->formatStateUsing(fn ($state): string => is_array($state)
                            ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                            : '(auto-generated from baseline — no custom schema stored)')
                        ->fontFamily('mono')
                        ->placeholder('Auto-generated'),
                ]),

        ]);
    }
}
