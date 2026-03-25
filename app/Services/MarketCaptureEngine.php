<?php

namespace App\Services;

use App\Jobs\GeneratePagePayloadJob;
use App\Models\CompetitorGap;
use App\Models\SeoOpportunity;
use App\Models\Service;
use App\Models\Site;
use App\Models\City;

class MarketCaptureEngine
{
    public function queueCompetitorGap(CompetitorGap $gap): ?SeoOpportunity
    {
        $site = Site::find($gap->site_id);

        if (! $site || ! $site->state_id) {
            return null;
        }

        [$service, $city] = $this->inferServiceAndCity($gap, $site);

        if (! $service || ! $city) {
            $gap->update(['status' => 'ignored']);

            return null;
        }

        $opportunity = SeoOpportunity::firstOrCreate(
            [
                'site_id' => $site->id,
                'service_id' => $service->id,
                'location_id' => $city->id,
            ],
            [
                'client_id' => $site->client_id,
                'opportunity_type' => 'content_gap',
                'status' => 'pending',
                'page_exists' => false,
                'target_keyword' => $gap->keyword_topic,
                'suggested_url' => '/' . $service->slug . '-' . $city->slug . '-' . strtolower($city->state->code ?? 'us'),
                'detection_source' => 'competitor_gap_discovery',
                'search_volume' => $gap->search_volume,
                'priority_score' => $gap->opportunity_score,
                'competitor_analysis' => [
                    'competitor_domain' => $gap->competitor_domain,
                    'competitor_url' => $gap->competitor_url,
                    'site_scan_run_id' => $gap->site_scan_run_id,
                    'competitor_scan_run_id' => $gap->competitor_scan_run_id,
                ],
            ],
        );

        $gap->update([
            'status' => 'queued',
            'evidence' => array_merge($gap->evidence ?? [], ['queued_opportunity_id' => $opportunity->id]),
        ]);

        return $opportunity;
    }

    public function generateDraftFromCompetitorGap(CompetitorGap $gap): ?SeoOpportunity
    {
        $opportunity = $this->queueCompetitorGap($gap);

        if (! $opportunity) {
            return null;
        }

        $opportunity->update(['status' => 'approved']);

        GeneratePagePayloadJob::dispatch($opportunity->id)->onQueue('generation');

        $gap->update(['status' => 'generated']);

        return $opportunity;
    }

    protected function inferServiceAndCity(CompetitorGap $gap, Site $site): array
    {
        $topic = strtolower($gap->keyword_topic);

        $service = Service::query()
            ->where('is_active', true)
            ->get()
            ->first(function (Service $service) use ($topic): bool {
                return str_contains($topic, strtolower($service->name)) || str_contains($topic, strtolower($service->slug));
            });

        $city = City::query()
            ->where('state_id', $site->state_id)
            ->get()
            ->first(function (City $city) use ($topic): bool {
                return str_contains($topic, strtolower($city->name)) || str_contains($topic, strtolower($city->slug));
            });

        return [$service, $city];
    }
}
