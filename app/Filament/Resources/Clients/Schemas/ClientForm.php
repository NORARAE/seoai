<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Client Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('John Doe'),

                        TextInput::make('company_name')
                            ->label('Company Name')
                            ->maxLength(255)
                            ->placeholder('Acme Corp'),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('client@example.com'),

                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('+1 (555) 123-4567'),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'archived' => 'Archived',
                            ])
                            ->default('active')
                            ->required(),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->maxLength(65535)
                            ->placeholder('Internal notes about this client...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
