@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-900 mb-2">Dashboard</h2>
    <p class="text-gray-600">Monitor your AI citation system performance and market coverage</p>
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
                    <p class="mt-1 text-sm text-green-700">Your scan is now in your dashboard under AI Scan Projects.</p>
                </div>
            </div>
        </div>
    </div>
@endif

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
                    <h3 class="text-lg font-bold text-gray-900">Action Queue</h3>
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

        <!-- AI Scan Projects Section -->
        <div id="ai-scans" class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">AI Scan Projects</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $totalScans }} {{ Str::plural('project', $totalScans) }} &mdash; each domain is an independent project</p>
                </div>
                <a href="{{ route('quick-scan.show') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Scan
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
                            <p class="text-xs font-medium text-green-700">↑ Progress detected — but {{ max(0, 100 - ($project->score ?? 0)) }}% of your market remains uncovered.</p>
                          </div>
                          @elseif($project->score_change !== null && $project->score_change === 0)
                          <div class="mt-3 p-2.5 bg-amber-50 border border-amber-200 rounded-lg">
                            <p class="text-xs font-medium text-amber-800">⚡ No improvement detected — your coverage hasn't evolved since your last scan. Competitors are pulling ahead.</p>
                          </div>
                          @elseif($project->score_change !== null && $project->score_change < 0)
                          <div class="mt-3 p-2.5 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-xs font-medium text-red-700">⚠ Your position is weakening — score dropped {{ abs($project->score_change) }} points. Without a coverage system, this trend continues.</p>
                          </div>
                          @else
                          <div class="mt-3 p-2.5 bg-gray-50 border border-gray-200 rounded-lg">
                            <p class="text-xs font-medium text-gray-600">Repeat scan — tracking changes reveals whether your market position is improving or eroding.</p>
                          </div>
                          @endif
                        @endif

                        @if($project->fastest_fix && $project->status === 'scanned')
                        <div class="mt-3 p-2.5 bg-blue-50 border border-blue-100 rounded-lg">
                            <p class="text-xs font-medium text-blue-600 uppercase tracking-wider mb-0.5">Fastest Fix</p>
                            <p class="text-xs text-blue-800 line-clamp-2">{{ $project->fastest_fix }}</p>
                        </div>
                        @endif

                        @if(!empty($project->issues) && is_array($project->issues))
                        <p class="text-xs text-gray-500 mt-3">{{ count($project->issues) }} coverage gap{{ count($project->issues) !== 1 ? 's' : '' }} detected</p>
                        @endif

                        {{-- Score-tiered action buttons --}}
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('quick-scan.result', ['scan_id' => $project->id, 'session_id' => $project->stripe_session_id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                View Report
                            </a>
                            @if(!$project->upgrade_plan)
                            <a href="{{ url('/pricing') }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                @if(($project->score ?? 0) < 40) Fix Structure @elseif(($project->score ?? 0) < 70) Improve Visibility @elseif(($project->score ?? 0) < 90) Expand Coverage @else Own Market @endif
                            </a>
                            @elseif(!$project->onboarding_submission_id)
                            <a href="{{ route('onboarding.start') }}?scan_id={{ $project->id }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Start Onboarding
                            </a>
                            @else
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 rounded-lg">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Onboarded
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Coverage System Packages -->
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
                    <p class="text-sm text-gray-600 mb-4">Full structural audit, entity architecture deployment, content connectivity mapping, and a system to expand your market footprint.</p>
                    <a href="{{ url('/pricing') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                        See Coverage Plans
                    </a>
                </div>
            </div>
            @else
            <!-- Empty state -->
            <div class="bg-white rounded-xl border-2 border-gray-100 p-8 shadow-sm text-center mb-6">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <p class="font-medium text-gray-900">No scan projects yet</p>
                <p class="text-sm text-gray-500 mt-1 mb-4">Run your first AI Citation Scan to add a project.</p>
                <a href="{{ route('quick-scan.show') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    Run Your First Scan
                </a>
            </div>
            @endif
        </div>

        <!-- Suggested Actions Panel -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-100 p-6 shadow-sm">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Expand Your Coverage</h3>
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
                {{-- Customer actions: scan-centric, no admin links --}}
                <a href="{{ route('quick-scan.show') }}" class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all group">
                    <div class="text-2xl mb-2">🔍</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Run a Scan</h4>
                    <p class="text-xs text-gray-600">Check your AI citation score</p>
                </a>

                <a href="{{ url('/pricing') }}" class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all group">
                    <div class="text-2xl mb-2">📈</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Upgrade Coverage</h4>
                    <p class="text-xs text-gray-600">See all coverage plans</p>
                </a>

                <a href="{{ url('/book') }}" class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all group">
                    <div class="text-2xl mb-2">📞</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Strategy Call</h4>
                    <p class="text-xs text-gray-600">Book a free consultation</p>
                </a>

                <a href="{{ url('/') }}" class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all group">
                    <div class="text-2xl mb-2">🏠</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Home</h4>
                    <p class="text-xs text-gray-600">Back to seoaico.com</p>
                </a>
                @endif
            </div>
        </div>

</div>
@endsection
