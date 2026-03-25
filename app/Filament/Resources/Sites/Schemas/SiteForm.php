<?php

namespace App\Filament\Resources\Sites\Schemas;

use App\Models\Client;
use App\Models\Site;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Site Information')
                    ->schema([
                        Select::make('client_id')
                            ->label('Client')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn (): ?int => request()->integer('client_id') ?: null)
                            ->nullable()
                            ->placeholder('Choose the client this site belongs to')
                            ->helperText(function (): string {
                                $clientId = request()->integer('client_id');

                                if (! $clientId) {
                                    return 'Sites belong to a client. If you start from a client record, this field is preselected.';
                                }

                                $clientName = Client::query()->whereKey($clientId)->value('name');

                                return $clientName
                                    ? "Adding this site under {$clientName}. You can change it before saving."
                                    : 'Sites belong to a client. If you start from a client record, this field is preselected.';
                            }),

                        TextInput::make('name')
                            ->label('Site Name')
                            ->maxLength(255)
                            ->placeholder('Acme Restoration'),

                        TextInput::make('domain')
                            ->label('Domain')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('example.com')
                            ->helperText('Enter the primary domain only, without https://, paths, or www')
                            ->rules([
                                'regex:/^[a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9]\.[a-zA-Z]{2,}$/',
                            ]),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'paused' => 'Paused',
                                'archived' => 'Archived',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Discovery')
                    ->schema([
                        Placeholder::make('discovery_intro')
                            ->label('What happens next')
                            ->content('We will automatically discover pages using your sitemap and internal links, then prepare the site for insights. You can fine-tune crawl behavior later if needed.')
                            ->columnSpanFull(),

                        Toggle::make('start_initial_scan')
                            ->label('Start discovery right away')
                            ->default(true)
                            ->helperText('Recommended. We will begin the first discovery pass as soon as the site is saved.'),

                        Hidden::make('sitemap_include_payload_pages')
                            ->default(true),

                        Hidden::make('sitemap_include_discovered_pages')
                            ->default(true),

                        Hidden::make('sitemap_max_urls_per_file')
                            ->default(500),
                    ])
                    ->columns(2)
                    ->visibleOn('create'),

                Section::make('Advanced Discovery Options')
                    ->schema([
                        Toggle::make('sitemap_enabled')
                            ->label('Prefer sitemap when available')
                            ->default(true)
                            ->helperText('Recommended for most sites. If enabled, discovery uses the sitemap first before expanding through internal links.'),

                        TextInput::make('initial_scan_depth_limit')
                            ->label('How deep to follow internal links')
                            ->numeric()
                            ->minValue(0)
                            ->default(4)
                            ->required()
                            ->helperText('Higher values explore more of the site during the first scan.'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->visibleOn('create'),

                Section::make('Advanced Discovery & Sitemap')
                    ->schema([
                        Toggle::make('sitemap_enabled')
                            ->label('Use sitemap for discovery')
                            ->helperText('Disable this if the site sitemap is broken or you want discovery to rely only on live crawling.')
                            ->default(true),

                        Toggle::make('sitemap_include_payload_pages')
                            ->label('Include generated payload pages in published sitemap')
                            ->default(true),

                        Toggle::make('sitemap_include_discovered_pages')
                            ->label('Include discovered live pages in published sitemap')
                            ->default(true),

                        TextInput::make('sitemap_max_urls_per_file')
                            ->label('Max URLs per sitemap file')
                            ->numeric()
                            ->minValue(1)
                            ->default(500)
                            ->required(),

                        Textarea::make('sitemap_manual_include_urls')
                            ->label('Manual include URLs')
                            ->rows(4)
                            ->placeholder("https://example.com/special-page\n/services/emergency-restoration"),

                        Textarea::make('sitemap_manual_exclude_urls')
                            ->label('Manual exclude URLs')
                            ->rows(4)
                            ->placeholder("https://example.com/private-page\n/about-us"),

                        Placeholder::make('sitemap_public_url')
                            ->label('Sitemap URL')
                            ->content(fn (?Site $record): string => $record?->sitemap_index_url ?? 'Available after site is created.'),
                    ])
                    ->columns(2)
                    ->hiddenOn('create'),
            ]);
    }
}
