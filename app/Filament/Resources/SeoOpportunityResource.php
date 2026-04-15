<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\FrontendDevRestricted;

use App\Filament\Resources\SeoOpportunityResource\Pages;
use App\Jobs\GeneratePagePayloadJob;
use App\Models\User;
use App\Models\SeoOpportunity;
use App\Support\CurrentScanResolver;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class SeoOpportunityResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = SeoOpportunity::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationLabel = 'SEO Opportunities';

    protected static string|\UnitEnum|null $navigationGroup = 'Scans & Discovery';

    protected static ?int $navigationSort = 6;

    public static function canViewAny(): bool
    {
        // frontend_dev users cannot access SEO opportunity data
        if (\App\Support\FrontendDevAccess::isRestricted()) {
            return false;
        }

        return Auth::check();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if (! $user instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isSuperAdmin()) {
            return $query;
        }

        $siteIds = $user->accessibleSites()->pluck('sites.id');

        if ($siteIds->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('site_id', $siteIds);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Opportunity Details')->schema([
                Forms\Components\Select::make('site_id')
                    ->relationship('site', 'name')
                    ->required(),
                Forms\Components\Select::make('opportunity_category')
                    ->options([
                        'missing_page' => 'Missing Page',
                        'optimization_candidate' => 'Optimization Candidate',
                        'structural_weakness' => 'Structural Weakness',
                        'coverage_gap' => 'Coverage Gap',
                    ])
                    ->required(),
                Forms\Components\Select::make('service_id')
                    ->relationship('service', 'name')
                    ->required(),
                Forms\Components\Select::make('location_id')
                    ->relationship('location', 'name')
                    ->label('City')
                    ->required(),
                Forms\Components\Select::make('opportunity_type')
                    ->options([
                        'new_page'       => 'New Page',
                        'underperforming' => 'Underperforming',
                        'high_volume'    => 'High Volume',
                        'quick_win'      => 'Quick Win',
                        'content_gap'    => 'Content Gap',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending'    => 'Pending',
                        'approved'   => 'Approved',
                        'in_progress' => 'In Progress',
                        'completed'  => 'Completed',
                        'dismissed'  => 'Dismissed',
                        'monitoring' => 'Monitoring',
                    ])
                    ->required(),
            ])->columns(2),

            Section::make('SEO Details')->schema([
                Forms\Components\TextInput::make('target_keyword')->maxLength(255),
                Forms\Components\TextInput::make('suggested_url')->maxLength(255),
                Forms\Components\TextInput::make('detection_source')->maxLength(255)->disabled(),
                Forms\Components\Toggle::make('page_exists')->label('Page Exists'),
            ])->columns(2),

            Section::make('Scoring')->schema([
                Forms\Components\TextInput::make('demand_score')->numeric()->disabled(),
                Forms\Components\TextInput::make('readiness_score')->numeric()->disabled(),
                Forms\Components\TextInput::make('business_value_score')->numeric()->disabled(),
                Forms\Components\TextInput::make('risk_score')->numeric()->disabled(),
                Forms\Components\TextInput::make('total_score')->numeric()->disabled(),
            ])->columns(3),

            Section::make('Recommendation')->schema([
                Forms\Components\Textarea::make('reason_summary')->rows(3)->columnSpanFull(),
                Forms\Components\Textarea::make('recommended_action')->rows(3)->columnSpanFull(),
            ]),

            Section::make('Notes')->schema([
                Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                $siteId = static::resolveTableSiteId();
                $scanRunId = static::resolveTableScanRunId();

                if (static::currentScanFilterRequested() && (! $siteId || ! $scanRunId)) {
                    return $query->whereRaw('1 = 0');
                }

                return $query->when(
                    $siteId,
                    fn (Builder $query, int $resolvedSiteId): Builder => $query->where('site_id', $resolvedSiteId)
                );
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('site.name')->label('Site')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('service.name')->label('Service')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('location.name')->label('City')->searchable()->sortable(),
                Tables\Columns\BadgeColumn::make('opportunity_category')
                    ->label('Category')
                    ->color(fn (?string $state): string => match ($state) {
                        'missing_page' => 'success',
                        'optimization_candidate' => 'warning',
                        'structural_weakness' => 'danger',
                        'coverage_gap' => 'info',
                        default => 'secondary',
                    }),
                Tables\Columns\BadgeColumn::make('opportunity_type')
                    ->label('Type')
                    ->color(fn (string $state): string => match ($state) {
                        'new_page'        => 'success',
                        'content_gap'     => 'info',
                        'quick_win'       => 'warning',
                        'underperforming' => 'danger',
                        'high_volume'     => 'primary',
                        default           => 'secondary',
                    }),
                Tables\Columns\BadgeColumn::make('status')
                    ->color(fn (string $state): string => match ($state) {
                        'pending'     => 'warning',
                        'approved'    => 'success',
                        'in_progress' => 'primary',
                        'completed'   => 'success',
                        'dismissed'   => 'secondary',
                        'monitoring'  => 'info',
                        default       => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('scan_visibility')
                    ->label('Scan Status')
                    ->badge()
                    ->state(function (SeoOpportunity $record): string {
                        $scanRunId = static::resolveTableScanRunId();

                        if (! $scanRunId) {
                            return 'No current scan';
                        }

                        return $record->scan_run_id === $scanRunId ? 'In current scan' : 'Older scan';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'In current scan' => 'success',
                        'Older scan' => 'gray',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('target_keyword')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('total_score')->label('Score')->sortable()->numeric(decimalPlaces: 1),
                Tables\Columns\TextColumn::make('risk_score')->label('Risk')->sortable()->numeric(decimalPlaces: 1)->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('coverage_confidence')
                    ->label('Confidence')
                    ->state(fn (SeoOpportunity $record): ?int => $record->signals['coverage_confidence'] ?? null)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('page_exists')
                    ->label('Page Exists')
                    ->boolean(),
                Tables\Columns\TextColumn::make('reason_summary')->label('Why')->limit(70)->wrap(),
                Tables\Columns\TextColumn::make('recommended_action')->label('Action')->limit(70)->wrap()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('detection_source')->label('Source'),
                Tables\Columns\TextColumn::make('identified_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'     => 'Pending',
                        'approved'    => 'Approved',
                        'in_progress' => 'In Progress',
                        'completed'   => 'Completed',
                        'dismissed'   => 'Dismissed',
                        'monitoring'  => 'Monitoring',
                    ]),
                Tables\Filters\SelectFilter::make('opportunity_type')
                    ->label('Type')
                    ->options([
                        'new_page'        => 'New Page',
                        'underperforming' => 'Underperforming',
                        'high_volume'     => 'High Volume',
                        'quick_win'       => 'Quick Win',
                        'content_gap'     => 'Content Gap',
                    ]),
                Tables\Filters\SelectFilter::make('opportunity_category')
                    ->label('Category')
                    ->options([
                        'missing_page' => 'Missing Page',
                        'optimization_candidate' => 'Optimization Candidate',
                        'structural_weakness' => 'Structural Weakness',
                        'coverage_gap' => 'Coverage Gap',
                    ]),
                Tables\Filters\SelectFilter::make('site_id')
                    ->label('Site')
                    ->relationship('site', 'name'),
                Tables\Filters\Filter::make('current_scan')
                    ->label('From current scan')
                    ->form([
                        Forms\Components\Hidden::make('scan_run_id'),
                        Forms\Components\Hidden::make('site_id'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $siteId = static::resolveFilterSiteId($data);
                        $scanRunId = static::resolveFilterScanRunId($data);

                        if (static::currentScanFilterRequested($data) && (! $siteId || ! $scanRunId)) {
                            return $query->whereRaw('1 = 0');
                        }

                        if (! $siteId || ! $scanRunId) {
                            return $query;
                        }

                        return $query
                            ->where('site_id', $siteId)
                            ->where('scan_run_id', $scanRunId);
                    })
                    ->indicateUsing(fn (): ?string => CurrentScanResolver::indicatorForUser(Auth::user(), static::requestedCurrentScanSiteId(), static::requestedCurrentScanRunId())),
                Tables\Filters\TernaryFilter::make('page_exists')
                    ->label('Page Exists'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (SeoOpportunity $record): bool => $record->status === 'pending')
                    ->action(function (SeoOpportunity $record): void {
                        $record->update(['status' => 'approved']);
                        Notification::make()->title('Opportunity approved')->success()->send();
                    }),

                Action::make('dismiss')
                    ->label('Dismiss')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (SeoOpportunity $record): bool => in_array($record->status, ['pending', 'approved'], true))
                    ->action(function (SeoOpportunity $record): void {
                        $record->update(['status' => 'dismissed']);
                        Notification::make()->title('Opportunity dismissed')->warning()->send();
                    }),

                Action::make('generate_payload')
                    ->label('Generate Payload')
                    ->icon('heroicon-o-bolt')
                    ->color('primary')
                    ->visible(fn (SeoOpportunity $record): bool => $record->status === 'approved' && ! $record->payload_id)
                    ->action(function (SeoOpportunity $record): void {
                        $record->update(['status' => 'in_progress']);
                        GeneratePagePayloadJob::dispatch($record->id);
                        Notification::make()->title('Payload generation queued')->success()->send();
                    }),

                EditAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('bulk_approve')
                    ->label('Approve Selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'approved']))
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('bulk_dismiss')
                    ->label('Dismiss Selected')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'dismissed']))
                    ->deselectRecordsAfterCompletion(),
            ])
            ->emptyStateHeading(static::currentScanFilterRequested()
                ? 'This snapshot did not create any opportunities'
                : 'No opportunities found')
            ->emptyStateDescription(static::currentScanFilterRequested()
                ? 'Review discovered pages first, or wait for the current scan to finish.'
                : 'No opportunities match the current filters.')
                ->defaultSort('total_score', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSeoOpportunities::route('/'),
            'create' => Pages\CreateSeoOpportunity::route('/create'),
            'edit'   => Pages\EditSeoOpportunity::route('/{record}/edit'),
        ];
    }

    protected static function requestedCurrentScanRunId(): ?int
    {
        $scanRunId = (int) data_get(request()->input('tableFilters', []), 'current_scan.scan_run_id');

        return $scanRunId > 0 ? $scanRunId : null;
    }

    protected static function requestedCurrentScanSiteId(): ?int
    {
        $siteId = (int) data_get(request()->input('tableFilters', []), 'current_scan.site_id');

        return $siteId > 0 ? $siteId : null;
    }

    protected static function resolveTableSiteId(): ?int
    {
        return CurrentScanResolver::resolveSiteForUser(Auth::user(), static::requestedCurrentScanSiteId())?->id;
    }

    protected static function resolveTableScanRunId(): ?int
    {
        return CurrentScanResolver::resolveIdForUser(
            Auth::user(),
            static::requestedCurrentScanSiteId(),
            static::requestedCurrentScanRunId(),
        );
    }

    /** @param array<string, mixed> $data */
    protected static function resolveFilterSiteId(array $data): ?int
    {
        $explicitSiteId = (int) ($data['site_id'] ?? 0);

        return CurrentScanResolver::resolveSiteForUser(Auth::user(), $explicitSiteId > 0 ? $explicitSiteId : null)?->id;
    }

    /** @param array<string, mixed> $data */
    protected static function resolveFilterScanRunId(array $data): ?int
    {
        $explicitSiteId = (int) ($data['site_id'] ?? 0);
        $explicitScanRunId = (int) ($data['scan_run_id'] ?? 0);

        return CurrentScanResolver::resolveIdForUser(
            Auth::user(),
            $explicitSiteId > 0 ? $explicitSiteId : null,
            $explicitScanRunId > 0 ? $explicitScanRunId : null,
        );
    }

    /** @param array<string, mixed>|null $data */
    protected static function currentScanFilterRequested(?array $data = null): bool
    {
        $siteId = (int) ($data['site_id'] ?? static::requestedCurrentScanSiteId() ?? 0);
        $scanRunId = (int) ($data['scan_run_id'] ?? static::requestedCurrentScanRunId() ?? 0);

        return $siteId > 0 || $scanRunId > 0;
    }
}
