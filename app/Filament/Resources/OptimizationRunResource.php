<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\FrontendDevRestricted;

use App\Enums\OptimizationStatus;
use App\Enums\OptimizationType;
use App\Filament\Resources\OptimizationRunResource\Pages;
use App\Models\OptimizationRun;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OptimizationRunResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = OptimizationRun::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Optimization Runs';

    protected static ?int $navigationSort = 102;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('site.domain')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('optimizable_type')
                    ->label('Target Type')
                    ->formatStateUsing(fn($state) => class_basename($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('optimizable_id')
                    ->label('Target ID'),
                Tables\Columns\TextColumn::make('optimization_type')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->label())
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->label())
                    ->color(fn($state) => $state->color())
                    ->sortable(),
                Tables\Columns\TextColumn::make('confidence_score')
                    ->label('Confidence')
                    ->formatStateUsing(fn($state) => $state ? $state . '%' : '-')
                    ->sortable(),
                Tables\Columns\IconColumn::make('auto_applied')
                    ->label('Auto')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('before_state_json')
                    ->label('Change Summary')
                    ->formatStateUsing(function ($record) {
                        $before = $record->before_state_json['title'] ?? null;
                        $after = $record->applied_state_json['title'] ?? $record->proposed_state_json['title'] ?? null;
                        
                        if ($before && $after && $before !== $after) {
                            return substr($before, 0, 30) . '... → ' . substr($after, 0, 30) . '...';
                        }
                        
                        return '-';
                    })
                    ->wrap()
                    ->limit(60),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('site_id')
                    ->relationship('site', 'domain')
                    ->label('Site'),
                Tables\Filters\SelectFilter::make('optimization_type')
                    ->options(collect(OptimizationType::cases())->pluck('name', 'value')-> toArray())
                    ->label('Type'),
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(OptimizationStatus::cases())->pluck('name', 'value')->toArray())
                    ->label('Status'),
                Tables\Filters\TernaryFilter::make('auto_applied')
                    ->label('Auto Applied'),
            ])
            ->actions([
                // View action not available in this Filament version
            ])
            ->bulkActions([
                // Bulk actions not available in this Filament version
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOptimizationRuns::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = OptimizationRun::where('status', OptimizationStatus::RECOMMENDED)->count();
            return $count > 0 ? (string) $count : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }
}
