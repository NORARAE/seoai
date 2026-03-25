<?php

namespace App\Filament\Widgets;

use App\Models\CompetitorDomain;
use App\Services\UsageTrackingService;
use App\Support\CurrentScanResolver;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class PlanAndLimitsPanelWidget extends Widget
{
    protected string $view = 'filament.widgets.plan-and-limits-panel-widget';

    protected int | string | array $columnSpan = 12;

    protected static ?int $sort = 8;

    protected function getViewData(): array
    {
        $context = CurrentScanResolver::dtoForUser(Auth::user());
        $site = $context->site;
        $client = $site?->client;
        $subscription = $client?->subscriptions()->whereIn('status', ['active', 'trial'])->latest()->with('plan')->first();
        $plan = $subscription?->plan;
        $usage = $client ? app(UsageTrackingService::class)->getUsageSummary($client) : [];

        $competitorDomains = $site
            ? CompetitorDomain::query()->where('site_id', $site->id)->get(['scan_count', 'paid_scan_credits'])
            : collect();

        $freeScansUsed = $competitorDomains->sum(fn (CompetitorDomain $domain): int => min(1, (int) $domain->scan_count));
        $creditsRemaining = $competitorDomains->sum(fn (CompetitorDomain $domain): int => (int) $domain->paid_scan_credits);
        $blockedReason = match (true) {
            $context->isLimited => 'crawl_limit',
            $competitorDomains->contains(fn (CompetitorDomain $domain): bool => $domain->scan_count >= 1 && $domain->paid_scan_credits < 1) => 'competitor_credit',
            default => null,
        };

        return [
            'context' => $context,
            'site' => $site,
            'plan' => $plan,
            'subscription' => $subscription,
            'usage' => $usage,
            'freeScansUsed' => $freeScansUsed,
            'creditsRemaining' => $creditsRemaining,
            'blockedReason' => $blockedReason,
        ];
    }
}