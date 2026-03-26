<?php

namespace App\Http\Controllers;

use App\Models\SeoKeyword;
use App\Models\SeoReport;
use App\Models\SeoTraffic;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function dashboard(Request $request)
    {
        $range = $request->query('range', '28d');

        // GSC data
        $gscTotals = SeoReport::where('report_type', 'gsc')
            ->where('dimension', 'totals')
            ->where('date_range', $range)
            ->first();

        $gscDateSeries = SeoReport::where('report_type', 'gsc')
            ->where('dimension', 'date')
            ->where('date_range', $range)
            ->first();

        // GA4 data
        $ga4Overview = SeoReport::where('report_type', 'ga4')
            ->where('dimension', 'overview')
            ->where('date_range', $range)
            ->first();

        $ga4SessionsSeries = SeoReport::where('report_type', 'ga4')
            ->where('dimension', 'sessions_series')
            ->where('date_range', $range)
            ->first();

        $ga4TrafficSources = SeoReport::where('report_type', 'ga4')
            ->where('dimension', 'traffic_sources')
            ->where('date_range', $range)
            ->first();

        $organicPct = SeoReport::where('report_type', 'ga4')
            ->where('dimension', 'organic_pct')
            ->where('date_range', $range)
            ->first();

        // Keywords & traffic from denormalized tables
        $keywords = SeoKeyword::where('date_range', $range)
            ->orderByDesc('clicks')
            ->take(20)
            ->get();

        $topPages = SeoReport::where('report_type', 'gsc')
            ->where('dimension', 'page')
            ->where('date_range', $range)
            ->first();

        return view('seo.dashboard', [
            'range'             => $range,
            'gscTotals'         => $gscTotals?->data ?? [],
            'gscDateSeries'     => $gscDateSeries?->data ?? [],
            'ga4Overview'       => $ga4Overview?->data ?? [],
            'ga4SessionsSeries' => $ga4SessionsSeries?->data ?? [],
            'ga4TrafficSources' => $ga4TrafficSources?->data ?? [],
            'organicPct'        => $organicPct?->data['organic_percentage'] ?? 0,
            'keywords'          => $keywords,
            'topPages'          => $topPages?->data ?? [],
        ]);
    }
}
