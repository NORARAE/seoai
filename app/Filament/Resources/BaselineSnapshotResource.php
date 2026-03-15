<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BaselineSnapshotResource\Pages;
use App\Models\BaselineSnapshot;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class BaselineSnapshotResource extends Resource
{
    protected static ?string $model = BaselineSnapshot::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-camera';

    protected static ?string $navigationLabel = 'Baseline Snapshots';

    protected static ?int $navigationSort = 103;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('site.domain')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('snapshotable_type')
                    ->label('Type')
                    ->formatStateUsing(fn($state) => class_basename($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('snapshotable_id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('snapshot_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('performance_snapshot_json')
                    ->label('Performance')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '-';
                        $clicks = $state['clicks'] ?? 0;
                        $impressions = $state['impressions'] ?? 0;
                        $ctr = isset($state['ctr']) ? round($state['ctr'] * 100, 2) . '%' : '-';
                        return "{$clicks} clicks, {$impressions} imp, {$ctr} CTR";
                    })
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('site_id')
                    ->relationship('site', 'domain')
                    ->label('Site'),
                Tables\Filters\SelectFilter::make('snapshotable_type')
                    ->options([
                        'App\\Models\\Page' => 'Page',
                        'App\\Models\\LocationPage' => 'Location Page',
                    ])
                    ->label('Type'),
            ])
            ->actions([
                // View action not available in this Filament version
            ])
            ->bulkActions([
                // Bulk actions not available in this Filament version
            ])
            ->defaultSort('snapshot_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBaselineSnapshots::route('/'),
        ];
    }
}
