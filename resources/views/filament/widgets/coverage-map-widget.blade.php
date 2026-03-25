<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Header --}}
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Coverage Map</h3>
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">Footprint View</span>
                </div>
                @if ($activeSiteDomain)
                    <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-gray-400">for {{ $activeSiteDomain }}</p>
                @endif
                <p class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400">A compact view of what already exists, what is still missing, and what your team has already generated.</p>
            </div>
            <x-filament::button tag="a" size="sm" color="gray" :href="$coverageMapUrl" icon="heroicon-o-map">
                Open Full Map
            </x-filament::button>
        </div>

        {{-- Coverage rows --}}
        <div class="mt-5 space-y-3">
            {{-- Existing --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900" style="border-left: 4px solid #22c55e;">
                <div class="mb-2 flex items-baseline justify-between">
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-2 rounded-full bg-green-500"></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Existing Pages</span>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold tabular-nums text-gray-900 dark:text-white">{{ number_format($existing) }}</span>
                        <span class="ml-1 text-xs text-gray-400">{{ $existingPct }}%</span>
                    </div>
                </div>
                <div class="h-2.5 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                    <div class="h-2.5 rounded-full bg-green-500 transition-all duration-700" style="width: {{ $existingPct }}%"></div>
                </div>
                <p class="mt-2 text-[11px] text-gray-400 dark:text-gray-500">Pages already live or confirmed in the current coverage matrix.</p>
            </div>

            {{-- Missing --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900" style="border-left: 4px solid #f59e0b;">
                <div class="mb-2 flex items-baseline justify-between">
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-2 rounded-full bg-amber-500"></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Missing Pages</span>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold tabular-nums text-gray-900 dark:text-white">{{ number_format($missing) }}</span>
                        <span class="ml-1 text-xs text-gray-400">{{ $missingPct }}%</span>
                    </div>
                </div>
                <div class="h-2.5 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                    <div class="h-2.5 rounded-full bg-amber-500 transition-all duration-700" style="width: {{ $missingPct }}%"></div>
                </div>
                <p class="mt-2 text-[11px] text-gray-400 dark:text-gray-500">Coverage gaps still available for opportunity review or page generation.</p>
            </div>

            {{-- Generated --}}
            <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-4 shadow-sm dark:border-indigo-900/50 dark:bg-indigo-950/30" style="border-left: 4px solid #6366f1;">
                <div class="flex items-baseline justify-between">
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-2 rounded-full bg-indigo-500"></div>
                        <span class="text-sm font-medium text-indigo-800 dark:text-indigo-300">Generated Pages</span>
                    </div>
                    <span class="text-2xl font-bold tabular-nums text-indigo-700 dark:text-indigo-300">{{ number_format($generated) }}</span>
                </div>
                <p class="mt-2 text-[11px] text-indigo-600/70 dark:text-indigo-400/70">Pages created from approved opportunities or expansion workflows.</p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
