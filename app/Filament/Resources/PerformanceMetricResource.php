<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerformanceMetricResource\Pages;
use App\Models\PerformanceMetric;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PerformanceMetricResource extends Resource
{
    protected static ?string $model = PerformanceMetric::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Performance Metrics';

    protected static ?int $navigationSort = 101;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('site.domain')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn($record) => $record->url),
                Tables\Columns\TextColumn::make('query')
                    ->searchable()
                    ->limit(30)
                    ->toggleable()
                    ->tooltip(fn($record) => $record->query),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('clicks')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('impressions')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ctr')
                    ->label('CTR')
                    ->formatStateUsing(fn($state) => round($state * 100, 2) . '%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('average_position')
                    ->label('Avg Pos')
                    ->formatStateUsing(fn($state) => round($state, 1))
                    ->sortable(),
                Tables\Columns\TextColumn::make('device')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('country')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('site_id')
                    ->relationship('site', 'domain')
                    ->label('Site'),
                Tables\Filters\Filter::make('low_ctr_opportunities')
                    ->label('Low CTR Opportunities')
                    ->query(fn(Builder $query): Builder => $query->lowCtrOpportunities(1000, 0.03))
                    ->toggle(),
            ])
            ->actions([
                // View action not available in this Filament version
            ])
            ->bulkActions([
                // Bulk actions not available in this Filament version
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerformanceMetrics::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = PerformanceMetric::lowCtrOpportunities(1000, 0.03)->count();
            return $count > 0 ? (string) $count : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
