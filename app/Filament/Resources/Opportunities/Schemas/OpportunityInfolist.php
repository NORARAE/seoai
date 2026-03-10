<?php

namespace App\Filament\Resources\Opportunities\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OpportunityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('site.name')
                    ->label('Site'),
                TextEntry::make('page.title')
                    ->label('Page'),
                TextEntry::make('issue_type'),
                TextEntry::make('priority_score')
                    ->numeric(),
                TextEntry::make('status'),
                TextEntry::make('recommendation')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
