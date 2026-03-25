<?php

namespace App\Filament\Pages;

use App\Models\City;
use App\Models\Service;
use App\Models\ServiceLocation;
use App\Models\Site;
use App\Models\State;
use App\Services\CoverageMatrixService;
use App\Services\LocationPageGeneratorService;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class CoverageMap extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected string $view = 'filament.pages.coverage-map';

    protected static ?string $navigationLabel = 'Coverage Map';

    protected static ?string $title = 'Coverage Intelligence Map';

    protected static UnitEnum|string|null $navigationGroup = 'Intelligence';

    protected static ?int $navigationSort = 3;

    public ?State $selectedState = null;
    public ?Service $selectedService = null;
    public array $matrix = [];
    public array $stats = [];
    public bool $showAll = false;
    public int $minPriorityScore = 60;

    protected $queryString = [
        'selectedState' => ['except' => null],
        'selectedService' => ['except' => null],
        'showAll' => ['except' => false],
        'minPriorityScore' => ['except' => 60],
    ];

    public function mount(): void
    {
        // Default to first active site's state
        $site = Site::where('is_active', true)->first();
        if ($site && !$this->selectedState) {
            $this->selectedState = $site->state;
        }

        $this->loadMatrix();
    }

    public function loadMatrix(): void
    {
        if (!$this->selectedState) {
            return;
        }

        $coverageService = app(CoverageMatrixService::class);

        // Get coverage stats
        $this->stats = $coverageService->getCoverageStats($this->selectedState);

        // Load matrix data
        $serviceIds = $this->selectedService ? [$this->selectedService->id] : null;
        $this->matrix = $coverageService->getMatrix($this->selectedState, $serviceIds);
    }

    public function refreshMatrix(): void
    {
        if (!$this->selectedState) {
            Notification::make()
                ->warning()
                ->title('No state selected')
                ->body('Please select a state to refresh the matrix.')
                ->send();
            return;
        }

        $coverageService = app(CoverageMatrixService::class);
        $result = $coverageService->buildMatrix($this->selectedState, $this->selectedService);

        $this->loadMatrix();

        Notification::make()
            ->success()
            ->title('Matrix refreshed')
            ->body("Analyzed {$result['combinations']} service-location combinations. Created {$result['created']}, updated {$result['updated']}.")
            ->send();
    }

    public function generatePage(int $serviceLocationId): void
    {
        $serviceLocation = ServiceLocation::findOrFail($serviceLocationId);
        $site = Site::where('state_id', $this->selectedState->id)
            ->where('is_active', true)
            ->first();

        if (!$site) {
            Notification::make()
                ->danger()
                ->title('No active site')
                ->body('Cannot generate page: no active site found for this state.')
                ->send();
            return;
        }

        $generator = app(LocationPageGeneratorService::class);
        $result = $generator->generateFromOpportunity($serviceLocation, $site);

        if ($result['success']) {
            $this->loadMatrix();

            Notification::make()
                ->success()
                ->title('Page generated')
                ->body("Successfully created location page with score {$result['content_score']}/100.")
                ->actions([
                    \Filament\Notifications\Actions\Action::make('view')
                        ->button()
                        ->url(route('filament.admin.resources.location-pages.view', ['record' => $result['location_page_id']]))
                ])
                ->send();
        } else {
            Notification::make()
                ->danger()
                ->title('Generation failed')
                ->body($result['error'])
                ->send();
        }
    }

    public function generateBatch(int $count = 10): void
    {
        $site = Site::where('state_id', $this->selectedState->id)
            ->where('is_active', true)
            ->first();

        if (!$site) {
            Notification::make()
                ->danger()
                ->title('No active site')
                ->body('Cannot generate pages: no active site found for this state.')
                ->send();
            return;
        }

        $generator = app(LocationPageGeneratorService::class);
        $result = $generator->generateBatch($site, $count, $this->minPriorityScore);

        $this->loadMatrix();

        Notification::make()
            ->success()
            ->title('Batch generation complete')
            ->body("Successfully generated {$result['successful']} pages. {$result['failed']} failed.")
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ServiceLocation::query()
                    ->when($this->selectedState, fn($q) => $q->where('state_id', $this->selectedState->id))
                    ->when($this->selectedService, fn($q) => $q->where('service_id', $this->selectedService->id))
                    ->when(!$this->showAll, fn($q) => $q->missingPages())
                    ->with(['service', 'city', 'county', 'state'])
                    ->orderBy('priority_score', 'desc')
            )
            ->columns([
                TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('city.name')
                    ->label('City')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('county.name')
                    ->label('County')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('priority_score')
                    ->label('Priority')
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => match(true) {
                        $record->priority_score >= 80 => 'success',
                        $record->priority_score >= 60 => 'warning',
                        default => 'gray'
                    }),
                TextColumn::make('traffic_potential')
                    ->label('Traffic Potential')
                    ->sortable(),
                TextColumn::make('estimated_monthly_searches')
                    ->label('Est. Searches')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn($record) => $record->status_color),
                TextColumn::make('avg_impressions_30d')
                    ->label('Impressions (30d)')
                    ->numeric()
                    ->default('—')
                    ->visible(fn() => $this->showAll),
                TextColumn::make('avg_clicks_30d')
                    ->label('Clicks (30d)')
                    ->numeric()
                    ->default('—')
                    ->visible(fn() => $this->showAll),
            ])
            ->filters([
                // Add filters as needed
            ])
            ->actions([
                Action::make('generate')
                    ->label('Generate Page')
                    ->icon('heroicon-o-plus-circle')
                    ->action(fn($record) => $this->generatePage($record->id))
                    ->visible(fn($record) => !$record->page_exists)
                    ->requiresConfirmation()
                    ->modalHeading('Generate Location Page')
                    ->modalDescription('This will create a new location page for this service-location combination.')
                    ->modalSubmitActionLabel('Generate'),
                Action::make('view')
                    ->label('View Page')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => route('filament.admin.resources.location-pages.view', ['record' => $record->location_page_id]))
                    ->visible(fn($record) => $record->page_exists)
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                // Add bulk actions as needed
            ])
            ->defaultSort('priority_score', 'desc')
            ->poll('60s'); // Auto-refresh every 60 seconds
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Could add widgets here
        ];
    }

    public function getViewData(): array
    {
        return [
            'stats' => $this->stats,
            'matrix' => $this->matrix,
            'services' => Service::where('is_active', true)->get(),
            'states' => State::all(),
        ];
    }
}
