@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-900 mb-2">Your Market Position</h2>
    <p class="text-gray-600">Your AI visibility baseline and coverage status</p>
</div>

@if(session('scan_saved'))
    <div class="mb-6">
        <div class="rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 011.414-1.414L8.414 12.172l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('scan_saved') }}</p>
                    <p class="mt-1 text-sm text-green-700">Your scan baseline has been saved. View it below under Your Visibility Baselines.</p>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- ═══════════════════════════════════════════════════ --}}
{{-- CUSTOMER: Your Current Position hero               --}}
{{-- ═══════════════════════════════════════════════════ --}}
@if(!auth()->user()?->isPrivilegedStaff() && !auth()->user()?->isFrontendDev())
    @if($scanProjects->count() > 0)
    @php
        $latestScan = $scanProjects->first();
    @endphp
    <div class="mb-8 bg-white rounded-xl border-2 border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Your Current Position</p>
        </div>
        <div class="p-6">
            <div class="flex items-start gap-6">
                <div class="flex-shrink-0 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full
                        @if(($latestScan->score ?? 0) >= 90) bg-gradient-to-br from-green-400 to-green-600
                        @elseif(($latestScan->score ?? 0) >= 70) bg-gradient-to-br from-blue-400 to-blue-600
                        @elseif(($latestScan->score ?? 0) >= 40) bg-gradient-to-br from-yellow-400 to-yellow-600
                        @else bg-gradient-to-br from-red-400 to-red-600
                        @endif text-white">
                        <span class="text-3xl font-bold">{{ $latestScan->score ?? '—' }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Latest Score</p>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold text-gray-900 truncate">{{ $latestScan->domain() }}</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        @if(($latestScan->score ?? 0) >= 90)
                            Your AI visibility is strong. Maintain your position and expand from here.
                        @elseif(($latestScan->score ?? 0) >= 70)
                            Your visibility is advancing. Key gaps remain that competitors can exploit.
                        @elseif(($latestScan->score ?? 0) >= 40)
                            Your coverage is partial. Significant gaps leave market share on the table.
                        @else
                            Your position needs attention. Most of your market is invisible to AI systems.
                        @endif
                    </p>
                    @if($latestScan->score_change !== null)
                    <p class="text-sm font-medium mt-2
                        @if($latestScan->score_change > 0) text-green-600
                        @elseif($latestScan->score_change < 0) text-red-600
                        @else text-gray-500
                        @endif
                    ">
                        @if($latestScan->score_change > 0) ↑ +{{ $latestScan->score_change }} points since last scan
                        @elseif($latestScan->score_change < 0) ↓ {{ $latestScan->score_change }} points since last scan
                        @else — No change since last scan
                        @endif
                    </p>
                    @endif
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('quick-scan.result', ['scan_id' => $latestScan->id, 'session_id' => $latestScan->stripe_session_id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                            View Full Report
                        </a>
                        <a href="{{ route('quick-scan.show') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-blue-300 text-gray-700 hover:text-blue-600 rounded-lg text-sm font-medium transition-colors">
                            Re-check Your Position
                        </a>
                    </div>
                </div>
            </div>
            @if($totalScans > 1)
            <div class="mt-6 pt-5 border-t border-gray-100 grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalScans }}</p>
                    <p class="text-xs text-gray-500">Total Scans</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ round($scanProjects->avg('score'), 0) }}</p>
                    <p class="text-xs text-gray-500">Average Score</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $scanProjects->pluck('url')->map(fn($u) => parse_url($u, PHP_URL_HOST) ?? $u)->unique()->count() }}</p>
                    <p class="text-xs text-gray-500">Domains Tracked</p>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
@endif

{{-- ═══════════════════════════════════════════════════ --}}
{{-- STAFF ONLY: Location page stats, health, actions   --}}
{{-- ═══════════════════════════════════════════════════ --}}
@if(auth()->user()?->isPrivilegedStaff() || auth()->user()?->isFrontendDev())

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
                label="Avg Citation Score" 
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
                    <h3 class="text-lg font-bold text-gray-900">Visibility Score</h3>
                    <span class="px-3 py-1 bg-{{ $health['color'] }}-100 text-{{ $health['color'] }}-800 rounded-full text-sm font-semibold border border-{{ $health['color'] }}-200">
                        {{ $health['grade'] }}
                    </span>
                </div>
                
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-32 h-32 rounded-full bg-gradient-to-br from-{{ $health['color'] }}-400 to-{{ $health['color'] }}-600 text-white mb-3">
                        <span class="text-4xl font-bold">{{ $health['score'] }}</span>
                    </div>
                    <p class="text-sm text-gray-600">Overall Visibility Score</p>
                </div>

                <div class="space-y-3">
                    <x-dashboard.progress-bar 
                        label="Render Completeness" 
                        :percentage="$health['metrics']['render']"
                        color="blue"
                    />
                    <x-dashboard.progress-bar 
                        label="Signal Completeness" 
                        :percentage="$health['metrics']['meta']"
                        color="green"
                    />
                    <x-dashboard.progress-bar 
                        label="Content Connectivity" 
                        :percentage="$health['metrics']['links']"
                        color="purple"
                    />
                    <x-dashboard.progress-bar 
                        label="Data Layer Readiness" 
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
                    <h3 class="text-lg font-bold text-gray-900">Priority Actions</h3>
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-semibold">
                        {{ array_sum($actionQueue) }} items
                    </span>
                </div>

                <div class="space-y-3">
                    <x-dashboard.action-item 
                        label="Pages Missing Signal Tags" 
                        :count="$actionQueue['missing_meta']"
                        color="orange"
                        :urgent="$actionQueue['missing_meta'] > 0"
                    />
                    <x-dashboard.action-item 
                        label="Pages Missing Content Connections" 
                        :count="$actionQueue['missing_internal_links']"
                        color="yellow"
                    />
                    <x-dashboard.action-item 
                        label="Pages Needing Render" 
                        :count="$actionQueue['needs_render']"
                        color="blue"
                    />
                    <x-dashboard.action-item 
                        label="Pages Below Citation Threshold (70)" 
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
                    @if(auth()->user()?->isPrivilegedStaff() || auth()->user()?->isFrontendDev())
                    <a href="/admin/location-pages" class="block w-full text-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        View All Pages in Admin
                    </a>
                    @endif
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
                                        @if(auth()->user()?->isPrivilegedStaff() || auth()->user()?->isFrontendDev())
                                        <span class="text-gray-300">|</span>
                                        <a href="/admin/location-pages/{{ $page['id'] }}" class="text-gray-600 hover:text-gray-900 font-medium">
                                            Edit
                                        </a>
                                        @endif
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

@endif {{-- End staff-only sections --}}

        <!-- AI Scan Projects Section -->
        <div id="ai-scans" class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Your Scan History</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $totalScans }} {{ Str::plural('baseline', $totalScans) }} — tracking your visibility across each domain</p>
                </div>
                <a href="{{ route('quick-scan.show') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Scan New Domain
                </a>
            </div>

            @if($scanProjects->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($scanProjects as $project)
                <div class="bg-white rounded-xl border-2 border-gray-100 shadow-sm hover:border-blue-200 hover:shadow-md transition-all overflow-hidden">
                    <!-- Score header band -->
                    <div class="px-5 py-3 flex items-center justify-between
                        @if($project->score >= 90) bg-green-50 border-b border-green-100
                        @elseif($project->score >= 70) bg-blue-50 border-b border-blue-100
                        @elseif($project->score >= 40) bg-yellow-50 border-b border-yellow-100
                        @else bg-red-50 border-b border-red-100
                        @endif
                    ">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-sm font-bold
                                @if($project->score >= 90) bg-green-100 text-green-800
                                @elseif($project->score >= 70) bg-blue-100 text-blue-800
                                @elseif($project->score >= 40) bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif
                            ">{{ $project->score ?? '—' }}</span>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider
                                    @if($project->score >= 90) text-green-700
                                    @elseif($project->score >= 70) text-blue-700
                                    @elseif($project->score >= 40) text-yellow-700
                                    @else text-red-700
                                    @endif
                                ">
                                    @if($project->score >= 90) Strong
                                    @elseif($project->score >= 70) Advancing
                                    @elseif($project->score >= 40) Partial
                                    @else Needs Work
                                    @endif
                                </p>
                                @if($project->score_change !== null)
                                <p class="text-xs font-medium mt-0.5
                                    @if($project->score_change > 0) text-green-600 @elseif($project->score_change < 0) text-red-600 @else text-gray-500 @endif
                                ">
                                    @if($project->score_change > 0) ↑ +{{ $project->score_change }} @elseif($project->score_change < 0) ↓ {{ $project->score_change }} @else — No change @endif
                                    since last scan
                                </p>
                                @endif
                            </div>
                        </div>
                        @if($project->upgrade_plan)
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-700">
                            {{ $project->upgrade_plan === 'authority-engine' ? 'Authority' : 'Citation' }}
                        </span>
                        @endif
                    </div>

                    <!-- Project body -->
                    <div class="p-5">
                        <h4 class="text-sm font-semibold text-gray-900 truncate" title="{{ $project->url }}">{{ $project->domain() }}</h4>
                        <p class="text-xs text-gray-500 mt-1">Scanned {{ $project->scanned_at?->diffForHumans() ?? $project->created_at->diffForHumans() }}</p>

                        {{-- Scan Progression / Stagnation Messaging --}}
                        @if($project->is_repeat_scan)
                          @if($project->score_change !== null && $project->score_change > 0)
                          <div class="mt-3 p-2.5 bg-green-50 border border-green-100 rounded-lg">
                            <p class="text-xs font-medium text-green-700">↑ Your position improved — but {{ max(0, 100 - ($project->score ?? 0)) }}% of your market remains uncovered.</p>
                          </div>
                          @elseif($project->score_change !== null && $project->score_change === 0)
                          <div class="mt-3 p-2.5 bg-amber-50 border border-amber-200 rounded-lg">
                            <p class="text-xs font-medium text-amber-800">⚡ Your position hasn't changed — without action, competitors continue pulling ahead.</p>
                          </div>
                          @elseif($project->score_change !== null && $project->score_change < 0)
                          <div class="mt-3 p-2.5 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-xs font-medium text-red-700">⚠ Your position weakened — dropped {{ abs($project->score_change) }} points. A coverage system stops this decline.</p>
                          </div>
                          @else
                          <div class="mt-3 p-2.5 bg-gray-50 border border-gray-200 rounded-lg">
                            <p class="text-xs font-medium text-gray-600">Repeat scan — tracking changes reveals whether your market position is improving or eroding.</p>
                          </div>
                          @endif
                        @endif

                        @if($project->fastest_fix && $project->status === 'scanned')
                        <div class="mt-3 p-2.5 bg-blue-50 border border-blue-100 rounded-lg">
                            <p class="text-xs font-medium text-blue-600 uppercase tracking-wider mb-0.5">Your Fastest Fix</p>
                            <p class="text-xs text-blue-800 line-clamp-2">{{ $project->fastest_fix }}</p>
                        </div>
                        @endif

                        @if(!empty($project->issues) && is_array($project->issues))
                        <p class="text-xs text-gray-500 mt-3">{{ count($project->issues) }} visibility gap{{ count($project->issues) !== 1 ? 's' : '' }} in your coverage</p>
                        @endif

                        {{-- Score-tiered action buttons --}}
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('quick-scan.result', ['scan_id' => $project->id, 'session_id' => $project->stripe_session_id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Your Report
                            </a>
                            @if(!$project->upgrade_plan)
                            <a href="{{ route('quick-scan.result', ['scan_id' => $project->id, 'session_id' => $project->stripe_session_id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                @if(($project->score ?? 0) < 40) Fix Your Structure @elseif(($project->score ?? 0) < 70) Improve Your Visibility @elseif(($project->score ?? 0) < 90) Expand Your Coverage @else Own Your Market @endif
                            </a>
                            @elseif($project->upgrade_plan === 'optimization' && !$project->onboarding_submission_id)
                            <a href="{{ route('onboarding.start') }}?scan_id={{ $project->id }}&plan=authority-engine" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Start Deployment
                            </a>
                            @elseif($project->onboarding_submission_id)
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 rounded-lg">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Deploying
                            </span>
                            @else
                            {{-- Mid-tier upgrade (diagnostic/fix-strategy) — link back to expanded report --}}
                            <a href="{{ route('quick-scan.result', ['scan_id' => $project->id, 'session_id' => $project->stripe_session_id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                Unlock Next Level
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Your Coverage Level -->
            @php
                $tierRank = $scanProjects->pluck('upgrade_plan')->filter()->map(function($plan) {
                    return match($plan) {
                        'diagnostic' => 1,
                        'fix-strategy' => 2,
                        'optimization' => 3,
                        'authority-engine' => 4,
                        default => 0,
                    };
                })->max() ?? 0;
                // Signal-based escalation: high-intent = multiple scans or tier 2+
                $dashHighIntent = ($totalScans ?? 0) >= 3 || $tierRank >= 2;
            @endphp

            @if($tierRank >= 3)
            {{-- User has optimization or authority-engine — system deployment path --}}
            @php
                $hasOnboarded = $scanProjects->whereNotNull('onboarding_submission_id')->count() > 0;
                $hasAuthorityEngine = $scanProjects->where('upgrade_plan', 'authority-engine')->count() > 0;
            @endphp

            @if($hasOnboarded || $hasAuthorityEngine)
            {{-- Already onboarded or on authority-engine — deployment in progress --}}
            <div class="mt-6 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border border-green-200 p-6">
                <div class="flex items-center gap-3 mb-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <h4 class="font-semibold text-gray-900">Your System Is Being Deployed</h4>
                </div>
                <p class="text-sm text-gray-600 mb-4">Your market infrastructure is being built. Continue scanning to track your trajectory as the system takes effect.</p>
                <a href="{{ route('quick-scan.show') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors">
                    Re-check Your Position
                </a>
            </div>
            @else
            {{-- Has $489 but hasn't onboarded — push to full system deployment --}}
            <div class="mt-6 bg-white rounded-xl border-2 {{ $dashHighIntent ? 'border-amber-400 shadow-md' : 'border-amber-200 shadow-sm' }} overflow-hidden">
                <div class="px-6 py-3 bg-gradient-to-r from-amber-50 to-yellow-50 border-b border-amber-100">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-amber-600">Ready For Deployment</p>
                </div>
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">You've seen your gaps. You know what's missing.</h4>
                    <p class="text-sm text-gray-600 mb-2">We build the structure that makes AI systems return you as the answer — entity architecture, content infrastructure, coverage defense — deployed and maintained.</p>
                    <p class="text-xs text-amber-700 mb-2 font-medium">Best for businesses ready to expand across multiple cities and services.</p>
                    <p class="text-sm text-gray-600 mb-4">Starting at $4,799+. <span class="text-gray-400 italic">Limited deployment capacity each month.</span></p>
                    <div class="flex flex-wrap gap-3">
                        @php $deployableScan = $scanProjects->where('upgrade_plan', 'optimization')->first() ?? $scanProjects->first(); @endphp
                        <a href="{{ route('onboarding.start', ['tier' => 'expansion', 'scan_id' => $deployableScan->id, 'plan' => 'authority-engine']) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                            Start System Deployment
                        </a>
                        <a href="{{ url('/book') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-amber-300 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                            Talk Strategy First
                        </a>
                    </div>
                </div>
            </div>
            @endif

            @elseif($tierRank === 2)
            {{-- User has fix-strategy ($249) — push toward $489 and intro full system --}}
            <div class="mt-6 bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl border border-indigo-200 p-6">
                <div class="mb-1">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400">Your Next Level</p>
                </div>
                <h4 class="font-semibold text-gray-900 mb-1">You've activated structural leverage — now deploy the full system</h4>
                <p class="text-sm text-gray-600 mb-2">You've unlocked the blueprint. System Activation ($489) puts it into motion — entity architecture, content connectivity, and live market coverage expansion.</p>
                <p class="text-xs text-gray-400 italic mb-4">Or skip ahead — we build the structure that makes AI return you as the answer. Limited builds each month.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ url('/pricing') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors">
                        See Full System Plans
                    </a>
                    @php $deployableScan = $scanProjects->where('upgrade_plan', 'fix-strategy')->first() ?? $scanProjects->first(); @endphp
                    <a href="{{ route('onboarding.start', ['tier' => 'expansion', 'scan_id' => $deployableScan->id, 'plan' => 'authority-engine']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-indigo-300 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                        Or Deploy Everything →
                    </a>
                </div>
                <p class="text-xs text-gray-400 mt-3 italic">Full Market Control starts at $4,799+ — we build it, you own it.</p>
            </div>

            @elseif($tierRank === 1)
            {{-- User has diagnostic ($99) — push toward $249 --}}
            <div class="mt-6 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl border border-blue-200 p-6 relative overflow-hidden">
                <div class="absolute top-3 right-3 px-2 py-0.5 bg-blue-600 text-white text-[10px] font-bold uppercase rounded-full">Your Next Level</div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-blue-400 mb-1">Level 3</p>
                <h4 class="font-semibold text-gray-900 mb-1">You've expanded your signals — now take structural control</h4>
                <p class="text-2xl font-bold text-blue-600 mb-2">$249 <span class="text-sm font-normal text-gray-500">per domain</span></p>
                <p class="text-sm text-gray-600 mb-4">Structural Leverage gives you the full audit, entity architecture, and content connectivity map. This is where the system becomes real.</p>
                <a href="{{ url('/pricing') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    See Coverage Plans
                </a>
                @if($dashHighIntent)
                <p class="text-xs text-gray-400 mt-3 italic">Or go further — we also build full systems. <a href="{{ route('onboarding.start', ['tier' => 'expansion', 'plan' => 'authority-engine']) }}" class="text-amber-600 hover:text-amber-700 underline">Learn about Full Deployment →</a></p>
                @endif
            </div>

            @else
            {{-- No upgrades yet — show both tiers, emphasize $249 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl border border-indigo-100 p-6">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-1">Level 2</p>
                    <h4 class="font-semibold text-gray-900 mb-1">Signal Expansion</h4>
                    <p class="text-2xl font-bold text-indigo-600 mb-2">$99 <span class="text-sm font-normal text-gray-500">per domain</span></p>
                    <p class="text-sm text-gray-600 mb-4">Expanded gap analysis, competitive signal mapping, and actionable fix priorities to strengthen your AI citation coverage.</p>
                    <a href="{{ url('/pricing') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors">
                        See Coverage Plans
                    </a>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl border border-blue-100 p-6 relative overflow-hidden">
                    <div class="absolute top-3 right-3 px-2 py-0.5 bg-blue-600 text-white text-[10px] font-bold uppercase rounded-full">Most Popular</div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-blue-400 mb-1">Level 3</p>
                    <h4 class="font-semibold text-gray-900 mb-1">Structural Leverage</h4>
                    <p class="text-2xl font-bold text-blue-600 mb-2">$249 <span class="text-sm font-normal text-gray-500">per domain</span></p>
                    <p class="text-sm text-gray-600 mb-4">Full structural audit, entity architecture, content connectivity mapping — where the system becomes real.</p>
                    <a href="{{ url('/pricing') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                        See Coverage Plans
                    </a>
                </div>
            </div>
            @endif
            @else
            <!-- Empty state -->
            <div class="bg-white rounded-xl border-2 border-gray-100 p-8 shadow-sm text-center mb-6">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <p class="font-medium text-gray-900">You haven't established your position yet</p>
                <p class="text-sm text-gray-500 mt-1 mb-4">Run your first AI Citation Scan to see where you stand — and what your competitors are doing that you're not.</p>
                <a href="{{ route('quick-scan.show') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    Check Your Position — $2
                </a>
            </div>
            @endif
        </div>

        <!-- Suggested Actions Panel -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-100 p-6 shadow-sm">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Your Next Move</h3>
                    <p class="text-sm text-gray-600">Actions that strengthen your market position</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @if(auth()->user()?->isPrivilegedStaff() || auth()->user()?->isFrontendDev())
                {{-- Staff actions: admin-linked --}}
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
                @else
                {{-- Customer actions: re-engagement and upgrade focused --}}
                <a href="{{ route('quick-scan.show') }}" class="block p-4 bg-white rounded-lg border border-blue-200 hover:border-blue-400 hover:shadow-md transition-all group ring-1 ring-blue-100">
                    <div class="text-2xl mb-2">🔄</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Re-check Your Position</h4>
                    <p class="text-xs text-gray-600">See if your visibility has changed</p>
                </a>

                <a href="{{ route('quick-scan.show') }}" class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all group">
                    <div class="text-2xl mb-2">🌐</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Scan New Domain</h4>
                    <p class="text-xs text-gray-600">Check another domain's position</p>
                </a>

                <a href="{{ url('/pricing') }}" class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all group">
                    <div class="text-2xl mb-2">📈</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Expand Your Coverage</h4>
                    <p class="text-xs text-gray-600">See all coverage plans</p>
                </a>

                @if(isset($tierRank) && $tierRank >= 2)
                <a href="{{ route('onboarding.start', ['tier' => 'expansion', 'plan' => 'authority-engine']) }}" class="block p-4 bg-white rounded-lg border border-amber-200 hover:border-amber-400 hover:shadow-md transition-all group">
                    <div class="text-2xl mb-2">🚀</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-amber-600">Deploy Your System</h4>
                    <p class="text-xs text-gray-600">We build it. AI returns you as the answer.</p>
                </a>
                @else
                <a href="{{ url('/book') }}" class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all group">
                    <div class="text-2xl mb-2">📞</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Strategy Call</h4>
                    <p class="text-xs text-gray-600">Book a free consultation</p>
                </a>
                @endif
                @endif
            </div>
        </div>

</div>
@endsection
