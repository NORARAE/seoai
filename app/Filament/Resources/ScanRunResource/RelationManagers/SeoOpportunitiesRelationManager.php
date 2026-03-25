<?php

namespace App\Filament\Resources\ScanRunResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SeoOpportunitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'seoOpportunities';

    protected static ?string $title = 'Opportunities';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'in_progress' => 'warning',
                        'completed' => 'gray',
                        'monitoring' => 'info',
                        'dismissed' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('opportunity_type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('target_keyword')
                    ->searchable()
                    ->limit(50)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('suggested_url')
                    ->label('Suggested URL')
                    ->limit(80)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('identified_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->emptyStateHeading('No opportunities were created from this scan run')
            ->emptyStateDescription('Opportunity detection has not produced any scan-scoped opportunities for this run yet.')
            ->defaultSort('id', 'asc');
    }
}