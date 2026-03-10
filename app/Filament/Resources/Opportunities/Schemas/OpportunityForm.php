<?php

namespace App\Filament\Resources\Opportunities\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OpportunityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('site_id')
                    ->relationship('site', 'name')
                    ->required(),
                Select::make('page_id')
                    ->relationship('page', 'title')
                    ->required(),
                TextInput::make('issue_type')
                    ->required(),
                TextInput::make('priority_score')
                    ->required()
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('open'),
                Textarea::make('recommendation')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
