<?php

namespace App\Filament\Resources\Sites\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                            ->nullable()
                            ->placeholder('Select a client'),

                        TextInput::make('name')
                            ->label('Site Name')
                            ->maxLength(255)
                            ->placeholder('My SEO Project'),

                        TextInput::make('domain')
                            ->label('Domain')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('example.com')
                            ->helperText('Enter domain without https:// or www')
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
            ]);
    }
}
