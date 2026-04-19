@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={corePlugins:{preflight:false}}</script>
<style>
/* ══════════════════════════════════════════════════════
   SEOAIco Dark Theme — Tailwind Color Overrides
   Maps light-mode Tailwind classes → dark luxe palette
   ══════════════════════════════════════════════════════ */

/* ── Neutral scale (light surfaces → dark) ────────── */
.bg-white { background-color: #0e0d09 !important; }
.bg-gray-50 { background-color: #13120e !important; }
.bg-gray-100 { background-color: rgba(200,168,75,.04) !important; }
.bg-gray-200 { background-color: rgba(200,168,75,.06) !important; }
.hover\:bg-gray-50:hover { background-color: rgba(200,168,75,.03) !important; }
.hover\:bg-gray-200:hover { background-color: rgba(200,168,75,.08) !important; }
.text-gray-900,.text-gray-800 { color: #ede8de !important; }
.text-gray-700 { color: rgba(237,232,222,.85) !important; }
.text-gray-600 { color: rgba(168,168,160,.72) !important; }
.text-gray-500 { color: rgba(168,168,160,.6) !important; }
.text-gray-400 { color: rgba(168,168,160,.45) !important; }
.text-gray-300 { color: rgba(168,168,160,.35) !important; }
.hover\:text-gray-900:hover { color: #ede8de !important; }
.border-gray-100,.border-gray-200 { border-color: rgba(200,168,75,.09) !important; }
.border-gray-300 { border-color: rgba(200,168,75,.12) !important; }
.border-gray-700 { border-color: rgba(200,168,75,.15) !important; }
.hover\:border-gray-500:hover { border-color: rgba(200,168,75,.2) !important; }
.divide-gray-200 > :not([hidden]) ~ :not([hidden]) { border-color: rgba(200,168,75,.09) !important; }
.bg-white\/10 { background-color: rgba(237,232,222,.1) !important; }

/* ── Blue → Gold ───────────────────────────────────── */
.bg-blue-50 { background-color: rgba(200,168,75,.04) !important; }
.bg-blue-100 { background-color: rgba(200,168,75,.08) !important; }
.bg-blue-500 { background-color: #c8a84b !important; }
.bg-blue-600 { background-color: #c8a84b !important; }
.hover\:bg-blue-700:hover { background-color: #a88a3a !important; }
.text-blue-300 { color: rgba(200,168,75,.5) !important; }
.text-blue-600,.text-blue-700,.text-blue-800 { color: #c8a84b !important; }
.hover\:text-blue-600:hover,.hover\:text-blue-900:hover { color: #c8a84b !important; }
.border-blue-100 { border-color: rgba(200,168,75,.08) !important; }
.border-blue-200 { border-color: rgba(200,168,75,.12) !important; }
.hover\:border-blue-200:hover { border-color: rgba(200,168,75,.12) !important; }
.hover\:border-blue-300:hover { border-color: rgba(200,168,75,.18) !important; }
.hover\:border-blue-400:hover { border-color: rgba(200,168,75,.22) !important; }

/* ── Indigo → Gold ─────────────────────────────────── */
.bg-indigo-50 { background-color: rgba(200,168,75,.04) !important; }
.bg-indigo-100 { background-color: rgba(200,168,75,.08) !important; }
.bg-indigo-600 { background-color: #c8a84b !important; }
.hover\:bg-indigo-700:hover { background-color: #a88a3a !important; }
.text-indigo-300 { color: rgba(200,168,75,.5) !important; }
.text-indigo-400 { color: rgba(200,168,75,.55) !important; }
.text-indigo-600,.text-indigo-700 { color: #c8a84b !important; }
.border-indigo-100 { border-color: rgba(200,168,75,.08) !important; }
.border-indigo-200 { border-color: rgba(200,168,75,.12) !important; }
.hover\:border-indigo-300:hover { border-color: rgba(200,168,75,.18) !important; }

/* ── Amber / Yellow → Gold ─────────────────────────── */
.bg-amber-50 { background-color: rgba(200,168,75,.06) !important; }
.bg-amber-100 { background-color: rgba(200,168,75,.1) !important; }
.border-amber-100 { border-color: rgba(200,168,75,.1) !important; }
.border-amber-200 { border-color: rgba(200,168,75,.18) !important; }
.text-amber-400\/80 { color: rgba(200,168,75,.8) !important; }
.bg-yellow-50 { background-color: rgba(200,168,75,.06) !important; }
.bg-yellow-100 { background-color: rgba(200,168,75,.1) !important; }
.border-yellow-200 { border-color: rgba(200,168,75,.18) !important; }
.bg-orange-50 { background-color: rgba(200,168,75,.06) !important; }
.bg-orange-100 { background-color: rgba(200,168,75,.08) !important; }
.text-orange-800 { color: #c8a84b !important; }
.border-orange-200 { border-color: rgba(200,168,75,.12) !important; }

/* ── Green (keep hue, adapt for dark) ──────────────── */
.bg-green-50 { background-color: rgba(106,175,144,.06) !important; }
.bg-green-100 { background-color: rgba(106,175,144,.12) !important; }
.bg-green-500 { background-color: #6aaf90 !important; }
.bg-green-600 { background-color: #5a9a7d !important; }
.hover\:bg-green-700:hover { background-color: #4d8a6f !important; }
.text-green-300 { color: rgba(106,175,144,.7) !important; }
.text-green-400,.text-green-500,.text-green-600,.text-green-700,.text-green-800 { color: #6aaf90 !important; }
.hover\:text-green-700:hover { color: #5a9a7d !important; }
.border-green-100 { border-color: rgba(106,175,144,.12) !important; }
.border-green-200 { border-color: rgba(106,175,144,.18) !important; }
.hover\:border-green-300:hover { border-color: rgba(106,175,144,.25) !important; }

/* ── Red (keep hue, adapt for dark) ────────────────── */
.bg-red-50 { background-color: rgba(196,120,120,.06) !important; }
.bg-red-100 { background-color: rgba(196,120,120,.12) !important; }
.bg-red-400,.bg-red-500 { background-color: #c47878 !important; }
.bg-red-600 { background-color: #b56868 !important; }
.hover\:bg-red-700:hover { background-color: #a55858 !important; }
.text-red-400,.text-red-500,.text-red-600,.text-red-700,.text-red-800 { color: #c47878 !important; }
.border-red-100 { border-color: rgba(196,120,120,.12) !important; }
.border-red-200 { border-color: rgba(196,120,120,.18) !important; }
.border-red-500 { border-color: #c47878 !important; }
.border-l-red-500 { border-left-color: #c47878 !important; }
.border-l-amber-400 { border-left-color: #c8a84b !important; }
.border-l-gray-300 { border-left-color: rgba(168,168,160,.35) !important; }

/* ── Emerald (green family) ────────────────────────── */
.bg-emerald-50 { background-color: rgba(106,175,144,.06) !important; }
.bg-emerald-500\/20 { background-color: rgba(106,175,144,.2) !important; }
.text-emerald-300 { color: rgba(106,175,144,.7) !important; }

/* ── Opacity variants ──────────────────────────────── */
.bg-blue-500\/20 { background-color: rgba(200,168,75,.2) !important; }
.bg-indigo-500\/20 { background-color: rgba(200,168,75,.2) !important; }
.text-white\/80 { color: rgba(237,232,222,.8) !important; }

/* ── White text (stays light on dark/colored bgs) ──── */
.text-white { color: #f5f0e8 !important; }

/* ── Gold-bg buttons: force dark text for contrast ─── */
.bg-blue-600,.bg-blue-600 *,
.bg-indigo-600,.bg-indigo-600 *,
.bg-amber-500,.bg-amber-500 *,
.hover\:bg-blue-700:hover,.hover\:bg-blue-700:hover *,
.hover\:bg-indigo-700:hover,.hover\:bg-indigo-700:hover *,
.hover\:bg-amber-600:hover,.hover\:bg-amber-600:hover * { color: #080808 !important; }

/* ── Gradient from/to overrides ────────────────────── */
.from-indigo-50 { --tw-gradient-from: rgba(200,168,75,.04) !important; }
.to-blue-50 { --tw-gradient-to: rgba(200,168,75,.02) !important; }
.from-blue-50 { --tw-gradient-from: rgba(200,168,75,.04) !important; }
.from-green-50,.from-emerald-50 { --tw-gradient-from: rgba(106,175,144,.04) !important; }
.to-green-50,.to-emerald-50 { --tw-gradient-to: rgba(106,175,144,.04) !important; }
.from-amber-50 { --tw-gradient-from: rgba(200,168,75,.04) !important; }
.to-yellow-50 { --tw-gradient-to: rgba(200,168,75,.02) !important; }
.to-cyan-50 { --tw-gradient-to: rgba(200,168,75,.04) !important; }
.from-blue-400 { --tw-gradient-from: #c8a84b !important; }
.to-blue-600 { --tw-gradient-to: #a88a3a !important; }
.from-white\/50 { --tw-gradient-from: rgba(14,13,9,.5) !important; }
.to-white\/90 { --tw-gradient-to: rgba(14,13,9,.9) !important; }

/* ── Rings ─────────────────────────────────────────── */
.ring-green-100 { --tw-ring-color: rgba(106,175,144,.1) !important; }
.ring-green-400\/30 { --tw-ring-color: rgba(106,175,144,.3) !important; }
.ring-amber-100 { --tw-ring-color: rgba(200,168,75,.1) !important; }
.ring-blue-100 { --tw-ring-color: rgba(200,168,75,.08) !important; }
.ring-blue-400\/30 { --tw-ring-color: rgba(200,168,75,.3) !important; }
.ring-indigo-400\/30 { --tw-ring-color: rgba(200,168,75,.3) !important; }
.ring-emerald-400\/30 { --tw-ring-color: rgba(106,175,144,.3) !important; }
.ring-red-50 { --tw-ring-color: rgba(196,120,120,.06) !important; }
.ring-white\/20 { --tw-ring-color: rgba(237,232,222,.2) !important; }

/* ── Shadows (deeper for dark theme) ───────────────── */
.shadow-sm { box-shadow: 0 1px 3px rgba(0,0,0,.3) !important; }
.shadow-md { box-shadow: 0 4px 10px rgba(0,0,0,.35) !important; }
.shadow-lg { box-shadow: 0 10px 24px rgba(0,0,0,.4) !important; }
.shadow-xl { box-shadow: 0 16px 40px rgba(0,0,0,.45) !important; }
.shadow-2xl { box-shadow: 0 24px 60px rgba(0,0,0,.5) !important; }
.hover\:shadow-md:hover { box-shadow: 0 4px 10px rgba(0,0,0,.35) !important; }
.hover\:shadow-lg:hover { box-shadow: 0 10px 24px rgba(0,0,0,.4) !important; }
.hover\:shadow-xl:hover { box-shadow: 0 16px 40px rgba(0,0,0,.45) !important; }

/* ── Group hover ───────────────────────────────────── */
.group:hover .group-hover\:text-blue-600 { color: #c8a84b !important; }
.group:hover .group-hover\:text-amber-600 { color: #c8a84b !important; }
</style>
@endpush

@section('content')
<style>
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(14px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes pulseGlow {
    0%, 100% { box-shadow: 0 0 0 0 rgba(200, 168, 75, 0.4); }
    50% { box-shadow: 0 0 24px 4px rgba(200, 168, 75, 0.15); }
}
.finding-card { will-change: transform, box-shadow; }
.cta-glow { transition: all 0.2s ease; }
.cta-glow:hover { box-shadow: 0 0 20px rgba(200, 168, 75, 0.35); transform: translateY(-1px); }
.cta-glow-red:hover { box-shadow: 0 0 20px rgba(196, 120, 120, 0.3); transform: translateY(-1px); }
.cta-glow-dark:hover { box-shadow: 0 0 24px rgba(0, 0, 0, 0.4); transform: translateY(-1px); }
.locked-teaser { backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); }
.pipeline-step { transition: all 0.3s ease; }
.pipeline-step:hover { transform: translateY(-2px); }
</style>
<!-- Page Header -->
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-900 mb-2">Your Market Position</h2>
    <p class="text-gray-600">Your AI visibility baseline and coverage status</p>
</div>

{{-- ═══════════════════════════════════════════════════ --}}
{{-- SYSTEM ENTRY CONFIRMATION (flash from checkout)    --}}
{{-- ═══════════════════════════════════════════════════ --}}
@if(session('system_entry'))
    <div class="mb-6">
        <div class="rounded-xl bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 p-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">You've entered the system at {{ $systemTier?->label() ?? 'Base' }}.</h3>
                    <p class="text-sm text-gray-600 mt-1">Everything builds forward from here.</p>
                    @if($nextStep)
                    <p class="text-sm text-indigo-600 font-medium mt-2">Next: {{ $nextStep }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

{{-- ═══════════════════════════════════════════════════ --}}
{{-- SYSTEM HEADER: You Are Here + Layer Progression    --}}
{{-- ═══════════════════════════════════════════════════ --}}
@if(!auth()->user()?->isPrivilegedStaff() && !auth()->user()?->isFrontendDev() && $tierRank > 0)
    <div class="mb-8 bg-white rounded-xl border-2 border-gray-100 shadow-sm overflow-hidden">
        {{-- Header bar --}}
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-900 to-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-amber-400/80 mb-1">Your System Level</p>
                    <h3 class="text-xl font-bold text-white">{{ $systemTier->label() }}</h3>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-sm font-semibold
                        @if($tierRank >= 4) bg-emerald-500/20 text-emerald-300 ring-1 ring-emerald-400/30
                        @elseif($tierRank >= 3) bg-blue-500/20 text-blue-300 ring-1 ring-blue-400/30
                        @elseif($tierRank >= 2) bg-indigo-500/20 text-indigo-300 ring-1 ring-indigo-400/30
                        @else bg-white/10 text-white/80 ring-1 ring-white/20
                        @endif
                    ">
                        @if($tierRank >= 4)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Full System Active
                        @else
                            {{ $tierRank }}/4 Layers Unlocked
                        @endif
                    </span>
                </div>
            </div>
            <p class="text-sm text-gray-400 mt-2">Everything builds forward from here.</p>
        </div>

        <div class="p-6">
            {{-- Horizontal progression pipeline --}}
            @php
                $pipelineSteps = [];
                $foundCurrent = false;
                foreach ($analysisLayers as $i => $layer) {
                    if ($layer['complete']) {
                        $pipelineSteps[] = array_merge($layer, ['state' => 'completed']);
                    } elseif (!$foundCurrent) {
                        $pipelineSteps[] = array_merge($layer, ['state' => 'current']);
                        $foundCurrent = true;
                    } else {
                        $pipelineSteps[] = array_merge($layer, ['state' => 'locked']);
                    }
                }
            @endphp
            <div class="flex items-center justify-between mb-6">
                @foreach($pipelineSteps as $step)
                <div class="pipeline-step flex-1 text-center relative {{ !$loop->last ? 'pr-2' : '' }}">
                    {{-- Connector arrow (between steps) --}}
                    @if(!$loop->last)
                    <div class="absolute top-5 right-0 w-full h-0.5 -mr-1 z-0
                        {{ $step['state'] === 'completed' ? 'bg-green-300' : 'bg-gray-200' }}
                    " style="left: 55%; width: 90%;"></div>
                    @endif

                    <div class="relative z-10">
                        @if($step['state'] === 'completed')
                        <div class="w-10 h-10 mx-auto mb-2 rounded-full bg-green-500 flex items-center justify-center shadow-md ring-4 ring-green-100">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p class="text-xs font-bold text-green-700">{{ $step['label'] }}</p>
                        <p class="text-[10px] text-green-500 font-semibold mt-0.5">Unlocked</p>
                        @elseif($step['state'] === 'current')
                        <div class="w-10 h-10 mx-auto mb-2 rounded-full bg-amber-500 flex items-center justify-center shadow-md ring-4 ring-amber-100" style="animation: pulseGlow 2s ease-in-out infinite">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </div>
                        <p class="text-xs font-bold text-amber-700">{{ $step['label'] }}</p>
                        <p class="text-[10px] text-amber-500 font-bold mt-0.5">{{ $step['price'] }} — Unlock Now</p>
                        @else
                        <div class="w-10 h-10 mx-auto mb-2 rounded-full bg-gray-200 flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="5" y="9" width="14" height="12" rx="2" stroke-width="1.5"/><path d="M8 9V6a4 4 0 118 0v3" stroke-width="1.5"/></svg>
                        </div>
                        <p class="text-xs font-semibold text-gray-400">{{ $step['label'] }}</p>
                        <p class="text-[10px] text-gray-300 mt-0.5">{{ $step['price'] }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Next recommended step (if not at max tier) --}}
            @if($nextStep && $nextRoute)
            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-xl">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-amber-600 mb-1">Next Layer Available</p>
                    <p class="text-sm font-medium text-gray-900">{{ $nextStep }}</p>
                </div>
                <a href="{{ route($nextRoute) }}" class="cta-glow inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-bold transition-all whitespace-nowrap shadow-md">
                    Unlock Now →
                </a>
            </div>
            @elseif($tierRank >= 4)
            <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-green-800">All analysis layers active.</p>
                    <p class="text-xs text-green-600 mt-0.5">Your system is fully deployed. <a href="{{ url('/book?entry=consultation') }}" class="underline hover:text-green-700">Book a consultation</a> to map the next expansion move.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
@endif

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
                        View All Pages
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
                <div class="bg-white rounded-xl border-2 border-gray-100 shadow-sm hover:border-blue-200 hover:shadow-lg hover:-translate-y-0.5 transition-all overflow-hidden">
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
                                @if(($project->score ?? 0) < 40) Resolve Your Gaps @elseif(($project->score ?? 0) < 70) Fix Your Visibility @elseif(($project->score ?? 0) < 90) Unlock Full Coverage @else Activate System @endif
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

            <!-- System-tier aware upgrade path -->
            @if($tierRank >= 4)
            {{-- Fully activated — point to strategy / deployment --}}
            <div class="mt-6 bg-gradient-to-br from-emerald-50 to-green-50 rounded-xl border border-green-200 p-6">
                <div class="flex items-center gap-3 mb-3">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <h4 class="font-semibold text-gray-900">Your System Is Fully Activated</h4>
                </div>
                <p class="text-sm text-gray-600 mb-4">All analysis layers are complete. The next step is implementation — we build the coverage infrastructure that makes AI systems return your business as the answer.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ url('/book?entry=consultation') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm">
                        Book Consultation
                    </a>
                    <a href="{{ route('quick-scan.show') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-green-300 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                        Re-check Your Position
                    </a>
                </div>
            </div>

            @elseif($tierRank >= 3)
            {{-- Has structural leverage ($249) — push to system activation ($489) --}}
            <div class="mt-6 bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl border border-indigo-200 p-6 hover:shadow-lg transition-all">
                <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-1">Your Next Level</p>
                <h4 class="font-semibold text-gray-900 mb-1">You've taken structural control — now activate the full system</h4>
                <p class="text-sm text-gray-600 mb-4">System Activation puts your strategy into motion — entity architecture, content connectivity, and live market coverage expansion.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('checkout.system-activation') }}" class="cta-glow inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold transition-all shadow-sm">
                        Activate Full System — $489 →
                    </a>
                    <a href="{{ url('/book?entry=consultation') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-indigo-300 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                        Book Consultation
                    </a>
                </div>
            </div>

            @elseif($tierRank >= 2)
            {{-- Has signal expansion ($99) — push to structural leverage ($249) --}}
            <div class="mt-6 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl border border-blue-200 p-6 relative overflow-hidden hover:shadow-lg transition-all">
                <div class="absolute top-3 right-3 px-2 py-0.5 bg-blue-600 text-white text-[10px] font-bold uppercase rounded-full">Your Next Level</div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-blue-400 mb-1">Level 3</p>
                <h4 class="font-semibold text-gray-900 mb-1">You've expanded your signals — now take structural control</h4>
                <p class="text-sm text-gray-600 mb-4">Structural Leverage gives you the full audit, entity architecture, and content connectivity map. This is where the system becomes real.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('checkout.structural-leverage') }}" class="cta-glow inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold transition-all shadow-sm">
                        Resolve Structural Gaps — $249 →
                    </a>
                    <a href="{{ route('checkout.system-activation') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-blue-300 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                        Or Activate Full System — $489
                    </a>
                </div>
            </div>

            @elseif($tierRank >= 1)
            {{-- Has base scan ($2) — show both $99 and $249 paths --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl border border-indigo-100 p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-1">Level 2</p>
                    <h4 class="font-semibold text-gray-900 mb-1">Signal Expansion</h4>
                    <p class="text-sm text-gray-600 mb-4">Expanded gap analysis, competitive signal mapping, and actionable fix priorities.</p>
                    <a href="{{ route('checkout.signal-expansion') }}" class="cta-glow inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold transition-all">
                        Unlock Signal Expansion — $99 →
                    </a>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl border border-blue-100 p-6 relative overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    <div class="absolute top-3 right-3 px-2 py-0.5 bg-blue-600 text-white text-[10px] font-bold uppercase rounded-full">Most Popular</div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-blue-400 mb-1">Level 3</p>
                    <h4 class="font-semibold text-gray-900 mb-1">Structural Leverage</h4>
                    <p class="text-sm text-gray-600 mb-4">Full structural audit, entity architecture, content connectivity — where the system becomes real.</p>
                    <a href="{{ route('checkout.structural-leverage') }}" class="cta-glow inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold transition-all">
                        Resolve Structural Gaps — $249 →
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
                @if($tierRank > 0)
                <p class="font-medium text-gray-900">You've entered the system at {{ $systemTier->label() }}.</p>
                <p class="text-sm text-gray-600 mt-1">Everything builds forward from here. Run your first scan to see where you stand.</p>
                @else
                <p class="font-medium text-gray-900">You haven't established your position yet</p>
                <p class="text-sm text-gray-500 mt-1 mb-4">Run your first AI Citation Scan to see where you stand — and what your competitors are doing that you're not.</p>
                @endif
                <div class="mt-4">
                <a href="{{ route('scan.start') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    Start Your Scan — $2
                </a>
                </div>
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

                <a href="/admin/location-pages?tableFilters[score][value][max]=70" class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all group">
                    <div class="text-2xl mb-2">📉</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Fix Low Scores</h4>
                    <p class="text-xs text-gray-600">Improve pages below 70</p>
                </a>

                <a href="/admin/funnel-dashboard" class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all group">
                    <div class="text-2xl mb-2">📊</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Review Funnel</h4>
                    <p class="text-xs text-gray-600">Inspect conversion and drop-off</p>
                </a>

                <a href="/admin" class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all group">
                    <div class="text-2xl mb-2">⚙️</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Open Admin Tools</h4>
                    <p class="text-xs text-gray-600">Use internal controls in admin only</p>
                </a>
                @else
                {{-- Customer actions: re-engagement and upgrade focused --}}
                <a href="{{ route('quick-scan.show') }}" class="block p-4 bg-white rounded-lg border border-blue-200 hover:border-blue-400 hover:shadow-lg hover:-translate-y-0.5 transition-all group ring-1 ring-blue-100">
                    <div class="text-2xl mb-2">🔄</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Re-check Your Position</h4>
                    <p class="text-xs text-gray-600">See if your visibility has changed</p>
                </a>

                <a href="{{ route('quick-scan.show') }}" class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-lg hover:-translate-y-0.5 transition-all group">
                    <div class="text-2xl mb-2">🌐</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Scan New Domain</h4>
                    <p class="text-xs text-gray-600">Check another domain's position</p>
                </a>

                <a href="{{ url('/pricing') }}" class="block p-4 bg-white rounded-lg border border-amber-200 hover:border-amber-400 hover:shadow-lg hover:-translate-y-0.5 transition-all group">
                    <div class="text-2xl mb-2">⚡</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-amber-600">Resolve Your Gaps</h4>
                    <p class="text-xs text-gray-600">Fix issues and unlock visibility</p>
                </a>

                @if(isset($tierRank) && $tierRank >= 2)
                <a href="{{ route('onboarding.start', ['tier' => 'expansion', 'plan' => 'authority-engine']) }}" class="block p-4 bg-white rounded-lg border border-amber-200 hover:border-amber-400 hover:shadow-lg hover:-translate-y-0.5 transition-all group">
                    <div class="text-2xl mb-2">🚀</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-amber-600">Activate System Deployment</h4>
                    <p class="text-xs text-gray-600">We build it. AI returns you as the answer.</p>
                </a>
                @else
                <a href="{{ url('/book?entry=consultation') }}" class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-lg hover:-translate-y-0.5 transition-all group">
                    <div class="text-2xl mb-2">📞</div>
                    <h4 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-600">Book Consultation</h4>
                    <p class="text-xs text-gray-600">We interpret your AI visibility and map your expansion path.</p>
                </a>
                @endif
                @endif
            </div>
        </div>

</div>

@push('scripts')
<script>
(function(){
  // Track upgrade CTA clicks
  document.querySelectorAll('a[href*="onboarding/start"], a[href*="checkout/"]').forEach(function(el){
    el.addEventListener('click',function(){
      fetch('/api/v1/track',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({event:'upgrade_cta_click',metadata:{label:el.textContent.trim().substring(0,60),href:el.getAttribute('href'),page:'dashboard'}})}).catch(function(){});
    });
  });

  // Staggered reveal for scan history cards
  var cards = document.querySelectorAll('#ai-scans .grid > div');
  cards.forEach(function(card, i){
    card.style.opacity = '0';
    card.style.transform = 'translateY(12px)';
    card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
    setTimeout(function(){
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    }, 100 + (i * 100));
  });
})();
</script>
@endpush

@endsection
