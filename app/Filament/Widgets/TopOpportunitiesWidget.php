<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\BuildsScanScopedLinks;
use App\Filament\Resources\SeoOpportunityResource;
use App\Jobs\GeneratePagePayloadJob;
use App\Models\SeoOpportunity;
use App\Support\CurrentScanResolver;
use App\Support\ScanContext;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TopOpportunitiesWidget extends BaseWidget
{
    use BuildsScanScopedLinks;

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 8;

    protected function getTableHeading(): string
    {
        $context = CurrentScanResolver::dtoForUser(Auth::user());
        $site = $context->site;

        return 'Best Opportunities To Review' . ($site ? " for {$site->domain}" : '');
    }

    public function table(Table $table): Table
    {
        $context = CurrentScanResolver::dtoForUser(Auth::user());

        return $table
            ->query($this->getTableQuery())
            ->description($context->scanRunId()
                ? 'Start with these recommended actions from the selected scan.'
                : 'Run a site scan to generate recommended actions.')
            ->headerActions([
                Action::make('open_admin_opportunities')
                    ->label('Open All Opportunities')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url($context->hasMetricsScan() ? $this->scanScopedUrl(SeoOpportunityResource::class, $context) : SeoOpportunityResource::getUrl())
                    ->color('gray'),
            ])
            ->columns([
                BadgeColumn::make('opportunity_type')
                    ->label('Type')
                    ->colors([
                        'success' => 'new_page',
                        'info' => 'content_gap',
                        'warning' => 'quick_win',
                        'danger' => 'underperforming',
                        'primary' => 'high_volume',
                    ])
                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title()),

                TextColumn::make('target_keyword')
                    ->label('Target Keyword')
                    ->searchable()
                    ->tooltip('Primary search phrase the page is expected to rank for')
                    ->limit(42),

                TextColumn::make('suggested_url')
                    ->label('Suggested URL')
                    ->copyable()
                    ->tooltip('Recommended URL structure for this opportunity')
                    ->limit(45),

                TextColumn::make('confidence_score')
                    ->label('Confidence')
                    ->state(fn (SeoOpportunity $record): string => $this->formatConfidence($record))
                    ->badge()
                    ->color(fn (SeoOpportunity $record): string => $this->confidenceColor($record))
                    ->description(fn (SeoOpportunity $record): string => $this->impactLabel($record))
                    ->tooltip('Confidence score combines priority and ranking potential signals'),

                BadgeColumn::make('impact_label')
                    ->label('Impact')
                    ->state(fn (SeoOpportunity $record): string => $this->impactLabel($record))
                    ->colors([
                        'danger' => 'High Impact',
                        'warning' => 'Medium Impact',
                        'gray' => 'Low Impact',
                    ]),

                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'primary' => 'in_progress',
                        'gray' => 'dismissed',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve Opportunity')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (SeoOpportunity $record): bool => $record->status === 'pending')
                    ->action(function (SeoOpportunity $record): void {
                        $record->update(['status' => 'approved']);
                        Notification::make()->title('Opportunity approved')->success()->send();
                    }),

                Action::make('reject')
                    ->label('Reject Opportunity')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (SeoOpportunity $record): bool => in_array($record->status, ['pending', 'approved'], true))
                    ->action(function (SeoOpportunity $record): void {
                        $record->update(['status' => 'dismissed']);
                        Notification::make()->title('Opportunity rejected')->warning()->send();
                    }),

                Action::make('generate_payload')
                    ->label('Generate Payload')
                    ->icon('heroicon-o-bolt')
                    ->color('primary')
                    ->visible(fn (SeoOpportunity $record): bool => $record->status === 'approved' && ! $record->payload_id)
                    ->action(function (SeoOpportunity $record): void {
                        $record->update(['status' => 'in_progress']);
                        GeneratePagePayloadJob::dispatch($record->id)->onQueue('generation');
                        Notification::make()->title('Payload generation queued')->success()->send();
                    }),
            ])
            ->defaultSort('priority_score', 'desc')
                ->emptyStateHeading('No opportunities are ready to review yet')
                ->emptyStateDescription('Run a site scan, then refresh opportunities to see what the platform recommends next.')
            ->poll('30s');
    }

    protected function getTableQuery(): Builder
    {
        $context = CurrentScanResolver::dtoForUser(Auth::user());
        $site = $context->site;
        $scanRun = $context->metricsScan;

        return SeoOpportunity::query()
            ->when($site?->id, fn (Builder $query) => $query->where('site_id', $site->id))
            ->when($scanRun?->id, fn (Builder $query, int $scanRunId) => $query->where('scan_run_id', $scanRunId))
            ->when(! $scanRun && $site?->id, fn (Builder $query) => $query->whereRaw('1 = 0'))
            ->whereIn('status', ['pending', 'approved', 'in_progress'])
            ->orderByDesc('priority_score')
                ->limit(5);
    }

    protected function formatConfidence(SeoOpportunity $record): string
    {
        $confidence = data_get($record->keyword_data, 'confidence_score');

        if (is_numeric($confidence)) {
            return (int) round((float) $confidence) . '%';
        }

        $fallback = is_numeric($record->priority_score) ? (float) $record->priority_score : 0.0;

        return (int) round(max(0, min(100, $fallback))) . '%';
    }

    protected function confidenceColor(SeoOpportunity $record): string
    {
        $score = (int) rtrim($this->formatConfidence($record), '%');

        return match (true) {
            $score >= 80 => 'success',
            $score >= 60 => 'warning',
            default => 'gray',
        };
    }

    protected function impactLabel(SeoOpportunity $record): string
    {
        $score = (int) rtrim($this->formatConfidence($record), '%');

        return match (true) {
            $score >= 80 => 'High Impact',
            $score >= 60 => 'Medium Impact',
            default => 'Low Impact',
        };
    }
}
