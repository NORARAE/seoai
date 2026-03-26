@extends('layouts.app')

@section('title', 'SEO Dashboard')

@push('styles')
<style>
    .kpi-card { @apply bg-white rounded-xl shadow-sm border border-gray-200 p-5; }
    .kpi-label { @apply text-xs font-medium text-gray-500 uppercase tracking-wider; }
    .kpi-value { @apply text-2xl font-bold text-gray-900 mt-1; }
    .chart-card { @apply bg-white rounded-xl shadow-sm border border-gray-200 p-5; }
    .chart-title { @apply text-sm font-semibold text-gray-700 mb-3; }
    .range-btn { @apply px-3 py-1.5 text-xs font-medium rounded-md transition-colors; }
    .range-btn.active { @apply bg-blue-600 text-white; }
    .range-btn:not(.active) { @apply bg-gray-100 text-gray-600 hover:bg-gray-200; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">SEO Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Google Search Console &amp; GA4 performance</p>
        </div>
        <div class="flex gap-1">
            @foreach (['7d' => '7 days', '28d' => '28 days', '90d' => '90 days'] as $val => $label)
                <a href="?range={{ $val }}"
                   class="range-btn {{ $range === $val ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
        <div class="kpi-card">
            <p class="kpi-label">Clicks</p>
            <p class="kpi-value">{{ number_format($gscTotals['clicks'] ?? 0) }}</p>
        </div>
        <div class="kpi-card">
            <p class="kpi-label">Impressions</p>
            <p class="kpi-value">{{ number_format($gscTotals['impressions'] ?? 0) }}</p>
        </div>
        <div class="kpi-card">
            <p class="kpi-label">Avg Position</p>
            <p class="kpi-value">{{ number_format($gscTotals['position'] ?? 0, 1) }}</p>
        </div>
        <div class="kpi-card">
            <p class="kpi-label">Sessions</p>
            <p class="kpi-value">{{ number_format($ga4Overview['sessions'] ?? 0) }}</p>
        </div>
        <div class="kpi-card">
            <p class="kpi-label">Organic %</p>
            <p class="kpi-value">{{ number_format($organicPct, 1) }}%</p>
        </div>
    </div>

    {{-- Charts Row 1: Clicks+Impressions & Sessions --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="chart-card">
            <h3 class="chart-title">Clicks &amp; Impressions</h3>
            <canvas id="gscLineChart" height="220"></canvas>
        </div>
        <div class="chart-card">
            <h3 class="chart-title">Sessions Over Time</h3>
            <canvas id="sessionsChart" height="220"></canvas>
        </div>
    </div>

    {{-- Charts Row 2: Top Keywords Bar & Traffic Sources Pie --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="chart-card">
            <h3 class="chart-title">Top 10 Keywords (Clicks)</h3>
            <canvas id="keywordsBarChart" height="260"></canvas>
        </div>
        <div class="chart-card">
            <h3 class="chart-title">Traffic Sources</h3>
            <canvas id="trafficPieChart" height="260"></canvas>
        </div>
    </div>

    {{-- Tables Row: Keywords & Top Pages --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Keywords Table --}}
        <div class="chart-card overflow-x-auto">
            <h3 class="chart-title">Top 20 Keywords</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b">
                        <th class="pb-2 pr-3">Query</th>
                        <th class="pb-2 pr-3 text-right">Clicks</th>
                        <th class="pb-2 pr-3 text-right">Impr</th>
                        <th class="pb-2 pr-3 text-right">CTR</th>
                        <th class="pb-2 text-right">Pos</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($keywords as $kw)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-2 pr-3 font-medium text-gray-800 truncate max-w-[200px]">{{ $kw->query }}</td>
                        <td class="py-2 pr-3 text-right">{{ number_format($kw->clicks) }}</td>
                        <td class="py-2 pr-3 text-right">{{ number_format($kw->impressions) }}</td>
                        <td class="py-2 pr-3 text-right">{{ number_format($kw->ctr * 100, 1) }}%</td>
                        <td class="py-2 text-right">{{ number_format($kw->position, 1) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-4 text-center text-gray-400">No keyword data yet. Run <code class="bg-gray-100 px-1 rounded">php artisan seo:fetch-gsc</code></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Top Pages Table --}}
        <div class="chart-card overflow-x-auto">
            <h3 class="chart-title">Top 10 Pages (GSC)</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b">
                        <th class="pb-2 pr-3">Page</th>
                        <th class="pb-2 pr-3 text-right">Clicks</th>
                        <th class="pb-2 pr-3 text-right">Impr</th>
                        <th class="pb-2 text-right">Pos</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse (array_slice($topPages, 0, 10) as $pg)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-2 pr-3 font-medium text-gray-800 truncate max-w-[250px]"
                            title="{{ $pg['keys'][0] ?? $pg['page'] ?? '' }}">
                            {{ basename($pg['keys'][0] ?? $pg['page'] ?? '/') ?: '/' }}
                        </td>
                        <td class="py-2 pr-3 text-right">{{ number_format($pg['clicks'] ?? 0) }}</td>
                        <td class="py-2 pr-3 text-right">{{ number_format($pg['impressions'] ?? 0) }}</td>
                        <td class="py-2 text-right">{{ number_format($pg['position'] ?? 0, 1) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-4 text-center text-gray-400">No page data yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    const gscSeries = @json($gscDateSeries);
    const ga4Sessions = @json($ga4SessionsSeries);
    const keywordsData = @json($keywords->take(10)->values());
    const trafficSources = @json($ga4TrafficSources);

    // ── Clicks & Impressions Line Chart ──
    if (gscSeries.length) {
        new Chart(document.getElementById('gscLineChart'), {
            type: 'line',
            data: {
                labels: gscSeries.map(r => r.keys?.[0] ?? r.date ?? ''),
                datasets: [
                    {
                        label: 'Clicks',
                        data: gscSeries.map(r => r.clicks ?? 0),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37,99,235,0.08)',
                        fill: true, tension: 0.3, pointRadius: 1,
                    },
                    {
                        label: 'Impressions',
                        data: gscSeries.map(r => r.impressions ?? 0),
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245,158,11,0.08)',
                        fill: true, tension: 0.3, pointRadius: 1,
                    },
                ],
            },
            options: { responsive: true, interaction: { mode: 'index', intersect: false }, scales: { y: { beginAtZero: true } } },
        });
    }

    // ── Sessions Line Chart ──
    if (ga4Sessions.length) {
        new Chart(document.getElementById('sessionsChart'), {
            type: 'line',
            data: {
                labels: ga4Sessions.map(r => r.date ?? ''),
                datasets: [{
                    label: 'Sessions',
                    data: ga4Sessions.map(r => r.sessions ?? 0),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.08)',
                    fill: true, tension: 0.3, pointRadius: 1,
                }],
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } },
        });
    }

    // ── Top Keywords Bar Chart ──
    if (keywordsData.length) {
        new Chart(document.getElementById('keywordsBarChart'), {
            type: 'bar',
            data: {
                labels: keywordsData.map(k => k.query?.substring(0, 25) ?? ''),
                datasets: [{
                    label: 'Clicks',
                    data: keywordsData.map(k => k.clicks ?? 0),
                    backgroundColor: '#6366f1',
                }],
            },
            options: {
                responsive: true, indexAxis: 'y',
                scales: { x: { beginAtZero: true } },
                plugins: { legend: { display: false } },
            },
        });
    }

    // ── Traffic Sources Pie Chart ──
    if (trafficSources.length) {
        const colors = ['#2563eb','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6','#f97316'];
        new Chart(document.getElementById('trafficPieChart'), {
            type: 'doughnut',
            data: {
                labels: trafficSources.map(s => s.source ?? 'Other'),
                datasets: [{
                    data: trafficSources.map(s => s.sessions ?? 0),
                    backgroundColor: colors.slice(0, trafficSources.length),
                }],
            },
            options: { responsive: true, plugins: { legend: { position: 'right' } } },
        });
    }
</script>
@endpush
