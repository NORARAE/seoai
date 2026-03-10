<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Page Details')
                    ->schema([
                        Select::make('site_id')
                            ->relationship('site', 'domain')
                            ->required()
                            ->disabled(),

                        TextInput::make('url')
                            ->label('URL')
                            ->required()
                            ->disabled(),

                        TextInput::make('path')
                            ->disabled(),

                        TextInput::make('title')
                            ->maxLength(255),

                        Select::make('crawl_status')
                            ->options([
                                'discovered' => 'Discovered',
                                'crawling' => 'Crawling',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                            ])
                            ->default('discovered'),
                    ])
                    ->columns(2),
            ]);
    }
}
