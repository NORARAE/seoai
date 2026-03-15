<?php

namespace App\Filament\Widgets;

use App\Models\ServiceLocation;
use App\Models\Site;
use App\Services\CoverageMatrixService;
use App\Services\LocationPageGeneratorService;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * ExpansionOpportunitiesWidget
 * 
 * Dashboard widget showing top expansion opportunities
 * from the coverage matrix
 */
class ExpansionOpportunitiesWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Top Expansion Opportunities';

    protected static ?string $maxHeight = '500px';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('priority_score')
                    ->label('Priority')
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => match(true) {
                        $record->priority_score >= 80 => 'success',
                        $record->priority_score >= 60 => 'warning',
                        default => 'gray'
                    })
                    ->icon(fn($record) => match(true) {
                        $record->priority_score >= 80 => 'heroicon-o-arrow-trending-up',
                        $record->priority_score >= 60 => 'heroicon-o-arrow-up',
                        default => 'heroicon-o-minus'
                    }),
                TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('city.name')
                    ->label('City')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->county?->name . ' County'),
                TextColumn::make('state.name')
                    ->label('State')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('traffic_potential')
                    ->label('Traffic Potential')
                    ->numeric()
                    ->sortable()
                    ->description('Estimated traffic score (0-100)')
                    ->color('warning'),
                TextColumn::make('estimated_monthly_searches')
                    ->label('Est. Monthly Searches')
                    ->numeric()
                    ->sortable()
                    ->description('Projected search volume'),
                TextColumn::make('last_analyzed_at')
                    ->label('Analyzed')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->defaultSort('priority_score', 'desc')
            ->actions([
                Action::make('generate')
                    ->label('Generate')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->action(fn($record) => $this->generatePage($record))
                    ->requiresConfirmation()
                    ->modalHeading('Generate Location Page')
                    ->modalDescription(fn($record) => 
                        "Generate a page for {$record->service->name} in {$record->city->name}, {$record->state->code}?"
                    )
                    ->modalSubmitActionLabel('Generate Page'),
                Action::make('view_details')
                    ->label('Details')
                    ->icon('heroicon-o-information-circle')
                    ->color('gray')
                    ->modalHeading('Opportunity Details')
                    ->modalContent(fn($record) => view('filament.widgets.opportunity-details', [
                        'opportunity' => $record
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->bulkActions([
                // Future: Add bulk generate action
            ])
            ->emptyStateHeading('No opportunities found')
            ->emptyStateDescription('All high-priority service-location combinations have been covered, or the coverage matrix needs to be refreshed.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->poll('120s'); // Auto-refresh every 2 minutes
    }

    protected function getTableQuery(): Builder
    {
        return ServiceLocation::query()
            ->missingPages()
            ->highPriority()
            ->with(['service', 'city', 'county', 'state'])
            ->limit(20);
    }

    protected function generatePage(ServiceLocation $serviceLocation): void
    {
        // Find active site for the state
        $site = Site::where('state_id', $serviceLocation->state_id)
            ->where('is_active', true)
            ->first();

        if (!$site) {
            Notification::make()
                ->danger()
                ->title('No active site found')
                ->body('Cannot generate page: no active site found for ' . $serviceLocation->state->name)
                ->send();
            return;
        }

        $generator = app(LocationPageGeneratorService::class);
        $result = $generator->generateFromOpportunity($serviceLocation, $site);

        if ($result['success']) {
            Notification::make()
                ->success()
                ->title('Page Generated!')
                ->body("Created page for {$serviceLocation->service->name} in {$serviceLocation->city->name} with score {$result['content_score']}/100")
                ->actions([
                    \Filament\Notifications\Actions\Action::make('view')
                        ->button()
                        ->url(route('filament.admin.resources.location-pages.view', ['record' => $result['location_page_id']]))
                        ->openUrlInNewTab()
                ])
                ->send();

            // Refresh the widget
            $this->dispatch('$refresh');
        } else {
            Notification::make()
                ->danger()
                ->title('Generation Failed')
                ->body($result['error'])
                ->send();
        }
    }

    public static function canView(): bool
    {
        // Could add permission checks here
        return true;
    }

    protected function getTableHeading(): string
    {
        $count = ServiceLocation::missingPages()->highPriority()->count();
        return "Top Expansion Opportunities ({$count} total)";
    }
}
