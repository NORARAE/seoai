<?php

namespace App\Filament\Resources\LocationPages\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LocationPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Page Information')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'archived' => 'Archived',
                            ])
                            ->required()
                            ->default('draft'),

                        Checkbox::make('is_indexable')
                            ->label('Allow Search Engine Indexing')
                            ->helperText('Uncheck to prevent this page from being indexed by search engines.')
                            ->default(true),

                        TextInput::make('score')
                            ->label('Quality Score')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->helperText('Score calculated during generation based on population, priority, etc.')
                            ->disabled(),
                    ])
                    ->columns(3),

                Section::make('Review Workflow')
                    ->schema([
                        Select::make('content_quality_status')
                            ->label('Quality Status')
                            ->options([
                                'unreviewed' => 'Unreviewed',
                                'edited' => 'Edited',
                                'approved' => 'Approved',
                                'excluded' => 'Excluded',
                            ])
                            ->required()
                            ->default('unreviewed')
                            ->helperText('Current review status of this page'),

                        Checkbox::make('needs_review')
                            ->label('Needs Review')
                            ->helperText('Check if this page requires review or re-review')
                            ->default(true),

                        Textarea::make('review_notes')
                            ->label('Review Notes')
                            ->rows(3)
                            ->helperText('Internal notes about content quality, edits needed, or approval reasons')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('SEO Content')
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Primary page title')
                            ->columnSpanFull(),

                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->required()
                            ->maxLength(255)
                            ->helperText('SEO meta title (shown in search results)')
                            ->columnSpanFull(),

                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->required()
                            ->maxLength(500)
                            ->rows(3)
                            ->helperText('SEO meta description (shown in search results, ~155 chars recommended)')
                            ->columnSpanFull(),

                        TextInput::make('h1')
                            ->label('H1 Heading')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Primary heading displayed on the page')
                            ->columnSpanFull(),
                    ]),

                Section::make('Body Content')
                    ->schema([
                        Textarea::make('body_sections_json')
                            ->label('Body Sections (JSON)')
                            ->rows(10)
                            ->helperText('Structured content sections in JSON format. Each section should have type, heading, and content.')
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state)
                            ->dehydrateStateUsing(fn ($state) => json_decode($state, true))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
