@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-900 mb-2">Dashboard</h2>
    <p class="text-gray-600">Monitor your SEO location intelligence system performance and health</p>
</div>

<!-- Summary Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <x-dashboard.stat-card 
                label="Total Location Pages" 
                :value="$stats['total_pages']"
                color="blue"
                icon="📄"
            />
            <x-dashboard.stat-card 
                label="Draft Pages" 
                :value="$stats['draft_pages']"
                color="gray"
                icon="✏️"
            />
            <x-dashboard.stat-card 
                label="Ready for Review" 
                :value="$stats['ready_for_review']"
                color="yellow"
                icon="👀"
            />
            <x-dashboard.stat-card 
                label="Published Pages" 
                :value="$stats['published_pages']"
                color="green"
                icon="✓"
            />
            <x-dashboard.stat-card 
                label="Avg SEO Score" 
                :value="$stats['average_score']"
                subtext="out of 100"
                color="blue"
                icon="⭐"
            />
        </div>

        <!-- System Health and Action Queue Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            
            <!-- System Health Card -->
            <div class="bg-white rounded-xl border-2 border-gray-100 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">System Health</h3>
                    <span class="px-3 py-1 bg-{{ $health['color'] }}-100 text-{{ $health['color'] }}-800 rounded-full text-sm font-semibold border border-{{ $health['color'] }}-200">
                        {{ $health['grade'] }}
                    </span>
                </div>
                
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-32 h-32 rounded-full bg-gradient-to-br from-{{ $health['color'] }}-400 to-{{ $health['color'] }}-600 text-white mb-3">
                        <span class="text-4xl font-bold">{{ $health['score'] }}</span>
                    </div>
                    <p class="text-sm text-gray-600">Overall Health Score</p>
                </div>

                <div class="space-y-3">
                    <x-dashboard.progress-bar 
                        label="Render Completeness" 
                        :percentage="$health['metrics']['render']"
                        color="blue"
                    />
                    <x-dashboard.progress-bar 
                        label="Meta Completeness" 
                        :percentage="$health['metrics']['meta']"
                        color="green"
                    />
                    <x-dashboard.progress-bar 
                        label="Internal Links" 
                        :percentage="$health['metrics']['links']"
                        color="purple"
                    />
                    <x-dashboard.progress-bar 
                        label="Schema Readiness" 
                        :percentage="$health['metrics']['schema']"
                        color="indigo"
                    />
                    <x-dashboard.progress-bar 
                        label="Content Quality" 
                        :percentage="$health['metrics']['quality']"
                        color="yellow"
                    />
                </div>
            </div>

            <!-- Action Queue Card -->
            <div class="bg-white rounded-xl border-2 border-gray-100 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Action Queue</h3>
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-semibold">
                        {{ array_sum($actionQueue) }} items
                    </span>
                </div>

                <div class="space-y-3">
                    <x-dashboard.action-item 
                        label="Pages Missing Meta Tags" 
                        :count="$actionQueue['missing_meta']"
                        color="orange"
                        :urgent="$actionQueue['missing_meta'] > 0"
                    />
                    <x-dashboard.action-item 
                        label="Pages Missing Internal Links" 
                        :count="$actionQueue['missing_internal_links']"
                        color="yellow"
                    />
                    <x-dashboard.action-item 
                        label="Pages Needing Render" 
                        :count="$actionQueue['needs_render']"
                        color="blue"
                    />
                    <x-dashboard.action-item 
                        label="Pages Below Score Threshold (70)" 
                        :count="$actionQueue['below_threshold']"
                        color="red"
                        :urgent="$actionQueue['below_threshold'] > 10"
                    />
                    <x-dashboard.action-item 
                        label="Unreviewed Pages" 
                        :count="$actionQueue['unreviewed']"
                        color="gray"
                    />
                </div>

                <div class="mt-6 pt-5 border-t border-gray-100">
                    <a href="/admin/location-pages" class="block w-full text-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        View All Pages in Admin
                    </a>
                </div>
            </div>

        </div>

        <!-- Recent Pages Table -->
        <div class="bg-white rounded-xl border-2 border-gray-100 shadow-sm mb-8">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Recent Pages</h3>
                <p class="text-sm text-gray-600 mt-1">Last 10 updated location pages</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentPages as $page)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $page['title'] }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded font-medium">
                                        {{ ucwords($page['type']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $page['location'] }}
                                </td>
                                <td class="px-6 py-4">
                                    <x-dashboard.status-badge :status="$page['status']" />
                                </td>
                                <td class="px-6 py-4">
                                    @if($page['score'])
                                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-sm font-bold
                                            @if($page['score'] >= 90) bg-green-100 text-green-800
                                            @elseif($page['score'] >= 80) bg-blue-100 text-blue-800
                                            @elseif($page['score'] >= 70) bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif
                                        ">
                                            {{ $page['score'] }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $page['updated_at']->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="/preview/{{ $page['slug'] }}" target="_blank" class="text-blue-600 hover:text-blue-900 font-medium">
                                            Preview
                                        </a>
                                        <span class="text-gray-300">|</span>
                                        <a href="/admin/location-pages/{{ $page['id'] }}" class="text-gray-600 hover:text-gray-900 font-medium">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="font-medium">No pages found</p>
                                    <p class="text-sm mt-1">Create location pages to get started</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- AI Scans Section -->
        <div id="ai-scans" class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">AI Citation Scans</h3>
                    <p class="text-sm text-gray-600 mt-1">Your AI citation readiness scores</p>
                </div>
                <a href="{{ route('quick-scan.show') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Scan
                </a>
            </div>

            @if($latestScan)
            <!-- Latest Scan Card -->
            <div class="bg-white rounded-xl border-2 border-gray-100 p-6 shadow-sm mb-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-5">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center text-xl font-bold
                                @if($latestScan->score >= 70) bg-green-100 text-green-700
                                @elseif($latestScan->score >= 40) bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700
                                @endif
                            ">
                                {{ $latestScan->score }}
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">{{ $latestScan->url }}</h4>
                            <p class="text-sm text-gray-500 mt-1">Scanned {{ $latestScan->scanned_at?->diffForHumans() ?? $latestScan->created_at->diffForHumans() }}</p>
                            @if($latestScan->fastest_fix)
                            <div class="mt-3 p-3 bg-blue-50 border border-blue-100 rounded-lg">
                                <p class="text-xs font-medium text-blue-700 uppercase tracking-wider mb-1">Fastest Fix</p>
                                <p class="text-sm text-blue-900">{{ $latestScan->fastest_fix }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($latestScan->score >= 70) bg-green-100 text-green-800
                            @elseif($latestScan->score >= 40) bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif
                        ">
                            @if($latestScan->score >= 70) Strong
                            @elseif($latestScan->score >= 40) Partial
                            @else Needs Work
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            @endif

            @if($scanHistory->count() > 0)
            <!-- Scan History -->
            <div class="bg-white rounded-xl border-2 border-gray-100 shadow-sm mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h4 class="text-base font-semibold text-gray-900">Scan History</h4>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($scanHistory as $scan)
                    <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-sm font-bold
                                @if($scan->score >= 70) bg-green-100 text-green-800
                                @elseif($scan->score >= 40) bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif
                            ">{{ $scan->score }}</span>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $scan->url }}</p>
                                <p class="text-xs text-gray-500">{{ $scan->scanned_at?->format('M j, Y g:i A') ?? $scan->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            @if(!empty($scan->issues) && is_array($scan->issues))
                            <span class="text-xs text-gray-500">{{ count($scan->issues) }} issue{{ count($scan->issues) !== 1 ? 's' : '' }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <!-- Empty state -->
            <div class="bg-white rounded-xl border-2 border-gray-100 p-8 shadow-sm text-center mb-6">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <p class="font-medium text-gray-900">No scans yet</p>
                <p class="text-sm text-gray-500 mt-1 mb-4">Run your first AI Citation Scan to see how your site performs.</p>
                <a href="{{ route('quick-scan.show') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    Run Your First Scan
                </a>
            </div>
            @endif

            <!-- Upgrade CTAs -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl border border-indigo-100 p-6">
                    <h4 class="font-semibold text-gray-900 mb-1">Citation Builder</h4>
                    <p class="text-2xl font-bold text-indigo-600 mb-2">$249</p>
                    <p class="text-sm text-gray-600 mb-4">Full opportunity mapping, FAQ optimization, entity structure, internal linking plan, and actionable fixes.</p>
                    <a href="{{ route('onboarding.start') }}?plan=citation-builder" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors">
                        Get Citation Builder
                    </a>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl border border-blue-100 p-6">
                    <h4 class="font-semibold text-gray-900 mb-1">Authority Engine</h4>
                    <p class="text-2xl font-bold text-blue-600 mb-2">$499</p>
                    <p class="text-sm text-gray-600 mb-4">Everything in Citation Builder plus AI-generated content, schema deployment, scoring system, and 4-month roadmap.</p>
                    <a href="{{ route('onboarding.start') }}?plan=authority-engine" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                        Get Authority Engine
                    </a>
                </div>
            </div>
        </div>

        <!-- Suggested Actions Panel -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-100 p-6 shadow-sm">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Suggested Actions</h3>
                    <p class="text-sm text-gray-600">Quick tasks to improve your SEO system</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="/admin/location-pages?tableFilters[content_quality_status][value]=unreviewed" class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all group">
                    <div class="text-2xl mb-2">👀</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Review Pages</h4>
                    <p class="text-xs text-gray-600">Check unreviewed content</p>
                </a>

                <button onclick="alert('Command: php artisan seo:render-location-pages --force')" class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all group text-left">
                    <div class="text-2xl mb-2">🔄</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Rebuild Cache</h4>
                    <p class="text-xs text-gray-600">Regenerate all renders</p>
                </button>

                <a href="/admin/location-pages?tableFilters[score][value][max]=70" class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all group">
                    <div class="text-2xl mb-2">📉</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Fix Low Scores</h4>
                    <p class="text-xs text-gray-600">Improve pages below 70</p>
                </a>

                <a href="/preview/biohazard-cleanup-seattle-wa" target="_blank" class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all group">
                    <div class="text-2xl mb-2">👁️</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Preview Sample</h4>
                    <p class="text-xs text-gray-600">View Seattle page</p>
                </a>
            </div>
        </div>

</div>
@endsection
