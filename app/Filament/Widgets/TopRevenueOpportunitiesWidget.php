<?php

namespace App\Filament\Widgets;

use App\Models\SeoOpportunity;
use App\Models\Site;
use App\Services\LocationPageGeneratorService;
use App\Services\RevenueOpportunityService;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * TopRevenueOpportunitiesWidget
 * 
 * Dashboard widget displaying highest revenue SEO opportunities
 * with one-click page generation
 */
class TopRevenueOpportunitiesWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Top Revenue Opportunities';

    protected static ?string $maxHeight = '600px';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('priority_score')
                    ->label('Priority')
                    ->numeric(decimalPlaces: 0)
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => $record->priority_badge_color)
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
                TextColumn::make('location_name')
                    ->label('Location')
                    ->searchable(['location.name', 'location.state.code'])
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->join('cities', 'seo_opportunities.location_id', '=', 'cities.id')
                            ->orderBy('cities.name', $direction);
                    }),
                TextColumn::make('opportunity_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn($record) => $record->opportunity_type_color)
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucwords($state, '_'))),
                TextColumn::make('search_volume')
                    ->label('Search Vol.')
                    ->numeric()
                    ->sortable()
                    ->description(fn($record) => $record->page_exists ? 'Page Exists' : 'New Opportunity'),
                TextColumn::make('estimated_monthly_revenue')
                    ->label('Est. Revenue')
                    ->money('USD')
                    ->sortable()
                    ->description(fn($record) => "@ {$record->rank_potential}% potential")
                    ->color('success')
                    ->weight('bold'),
                TextColumn::make('rank_potential')
                    ->label('Rank Potential')
                    ->numeric(decimalPlaces: 0)
                    ->suffix('%')
                    ->sortable()
                    ->color(fn($record) => match(true) {
                        $record->rank_potential >= 75 => 'success',
                        $record->rank_potential >= 50 => 'warning',
                        default => 'gray'
                    }),
                TextColumn::make('competition_score')
                    ->label('Competition')
                    ->numeric(decimalPlaces: 0)
                    ->sortable()
                    ->color(fn($record) => match(true) {
                        $record->competition_score >= 70 => 'danger',
                        $record->competition_score >= 50 => 'warning',
                        default => 'success'
                    })
                    ->description(fn($record) => $record->competition_score >= 70 ? 'High' : ($record->competition_score >= 50 ? 'Medium' : 'Low'))
                    ->toggleable(),
                TextColumn::make('current_position')
                    ->label('Current Pos.')
                    ->numeric()
                    ->default('—')
                    ->description('GSC Average')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn($record) => $record->status_color)
                    ->formatStateUsing(fn (string $state): string => ucwords($state))
                    ->toggleable(),
            ])
            ->defaultSort('priority_score', 'desc')
            ->actions([
                Action::make('generate')
                    ->label('Generate Page')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->visible(fn($record) => !$record->page_exists && in_array($record->status, ['pending', 'approved']))
                    ->action(fn($record) => $this->generatePage($record))
                    ->requiresConfirmation()
                    ->modalHeading('Generate Revenue Opportunity Page')
                    ->modalDescription(fn($record) => 
                        "Generate page for {$record->service_name} in {$record->location_name}?\n\n" .
                        "Estimated monthly revenue: \${$record->estimated_monthly_revenue}\n" .
                        "Search volume: {$record->search_volume}\n" .
                        "Rank potential: {$record->rank_potential}%"
                    )
                    ->modalSubmitActionLabel('Generate Page'),
                Action::make('view_page')
                    ->label('View Page')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->visible(fn($record) => $record->page_exists && $record->location_page_id)
                    ->url(fn($record) => route('filament.admin.resources.location-pages.view', ['record' => $record->location_page_id]))
                    ->openUrlInNewTab(),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->action(fn($record) => $this->approveOpportunity($record))
                    ->requiresConfirmation(),
                Action::make('dismiss')
                    ->label('Dismiss')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => in_array($record->status, ['pending', 'approved']))
                    ->action(fn($record) => $this->dismissOpportunity($record))
                    ->requiresConfirmation()
                    ->modalHeading('Dismiss Opportunity')
                    ->modalDescription('Are you sure you want to dismiss this opportunity? It will not appear in future recommendations.')
                    ->modalSubmitActionLabel('Dismiss'),
                Action::make('details')
                    ->label('Details')
                    ->icon('heroicon-o-information-circle')
                    ->color('gray')
                    ->modalHeading('Opportunity Details')
                    ->modalContent(fn($record) => view('filament.widgets.revenue-opportunity-details', [
                        'opportunity' => $record
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->bulkActions([
                // Future: Add bulk approve/dismiss actions
            ])
            ->emptyStateHeading('No revenue opportunities found')
            ->emptyStateDescription('Generate opportunities by scanning for service × location combinations with high revenue potential.')
            ->emptyStateIcon('heroicon-o-currency-dollar')
            ->emptyStateActions([
                Action::make('scan')
                    ->label('Scan for Opportunities')
                    ->icon('heroicon-o-magnifying-glass')
                    ->action(fn() => $this->scanForOpportunities()),
            ])
            ->poll('180s'); // Auto-refresh every 3 minutes
    }

    protected function getTableQuery(): Builder
    {
        // Get first active site for the user
        // TODO: Add multi-site filtering
        $site = Site::where('is_active', true)->first();

        if (!$site) {
            return SeoOpportunity::query()->whereRaw('1 = 0'); // Empty query
        }

        return SeoOpportunity::query()
            ->where('site_id', $site->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where('status', '!=', 'dismissed')
            ->with(['service', 'location', 'location.state', 'locationPage'])
            ->limit(20);
    }

    protected function generatePage(SeoOpportunity $opportunity): void
    {
        $site = Site::findOrFail($opportunity->site_id);

        // Check if already generating
        if ($opportunity->status === 'in_progress') {
            Notification::make()
                ->warning()
                ->title('Already in progress')
                ->body('This page is already being generated.')
                ->send();
            return;
        }

        // Mark as in progress
        $opportunity->update(['status' => 'in_progress']);

        try {
            $generator = app(LocationPageGeneratorService::class);
            
            // Create ServiceLocation if it doesn't exist
            $serviceLocation = \App\Models\ServiceLocation::firstOrCreate([
                'service_id' => $opportunity->service_id,
                'city_id' => $opportunity->location_id,
            ], [
                'state_id' => $opportunity->location->state_id,
                'county_id' => $opportunity->location->county_id,
                'page_exists' => false,
            ]);

            $result = $generator->generateFromOpportunity($serviceLocation, $site);

            if ($result['success']) {
                // Update opportunity
                $opportunity->markAsCompleted(
                    \App\Models\LocationPage::findOrFail($result['location_page_id'])
                );

                Notification::make()
                    ->success()
                    ->title('Page Generated!')
                    ->body("Successfully created revenue opportunity page with score {$result['content_score']}/100. Estimated monthly revenue: \${$opportunity->estimated_monthly_revenue}")
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->button()
                            ->url(route('filament.admin.resources.location-pages.view', ['record' => $result['location_page_id']]))
                            ->openUrlInNewTab()
                    ])
                    ->send();

                $this->dispatch('$refresh');
            } else {
                $opportunity->update(['status' => 'approved']); // Reset status
                
                Notification::make()
                    ->danger()
                    ->title('Generation Failed')
                    ->body($result['error'])
                    ->send();
            }
        } catch (\Exception $e) {
            $opportunity->update(['status' => 'approved']); // Reset status

            Notification::make()
                ->danger()
                ->title('Error')
                ->body('An error occurred: ' . $e->getMessage())
                ->send();
        }
    }

    protected function approveOpportunity(SeoOpportunity $opportunity): void
    {
        $opportunity->update(['status' => 'approved']);

        Notification::make()
            ->success()
            ->title('Opportunity Approved')
            ->body("Approved {$opportunity->service_name} in {$opportunity->location_name} for page generation.")
            ->send();

        $this->dispatch('$refresh');
    }

    protected function dismissOpportunity(SeoOpportunity $opportunity): void
    {
        $opportunity->update(['status' => 'dismissed']);

        Notification::make()
            ->success()
            ->title('Opportunity Dismissed')
            ->body('This opportunity will not appear in future recommendations.')
            ->send();

        $this->dispatch('$refresh');
    }

    protected function scanForOpportunities(): void
    {
        $site = Site::where('is_active', true)->first();

        if (!$site) {
            Notification::make()
                ->danger()
                ->title('No active site')
                ->body('No active site found to scan for opportunities.')
                ->send();
            return;
        }

        $revenueService = app(RevenueOpportunityService::class);
        $result = $revenueService->generateOpportunities($site, [
            'min_priority_score' => 60,
            'min_search_volume' => 20,
        ]);

        Notification::make()
            ->success()
            ->title('Scan Complete')
            ->body("Found {$result['created']} new opportunities and updated {$result['updated']} existing ones.")
            ->send();

        $this->dispatch('$refresh');
    }

    public static function canView(): bool
    {
        // Could add permission checks here
        return true;
    }

    protected function getTableHeading(): string
    {
        $site = Site::where('is_active', true)->first();
        
        if (!$site) {
            return 'Top Revenue Opportunities';
        }

        $count = SeoOpportunity::where('site_id', $site->id)
            ->whereIn('status', ['pending', 'approved'])
            ->count();

        $totalRevenue = SeoOpportunity::where('site_id', $site->id)
            ->whereIn('status', ['pending', 'approved'])
            ->sum('estimated_monthly_revenue');

        return "Top Revenue Opportunities ({$count} total, \$" . number_format($totalRevenue, 0) . "/mo potential)";
    }
}
