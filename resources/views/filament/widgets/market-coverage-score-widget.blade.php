<x-filament-widgets::widget>
    <x-filament::section>
        <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-700">Market Coverage Score</p>
        <h3 class="mt-2 text-sm font-semibold uppercase tracking-wide text-gray-500">{{ $domain ?? 'No active site' }}</h3>

        <div class="mt-3 flex items-end gap-2">
            <p class="text-4xl font-semibold text-emerald-700">{{ $coverageScore }}</p>
            <p class="pb-1 text-base text-gray-500">/ 100</p>
        </div>

        <div class="mt-4 space-y-2 text-sm">
            <div class="flex items-center justify-between"><span class="text-gray-600">Service Coverage</span><span class="font-medium">{{ $serviceCoverage }}%</span></div>
            <div class="flex items-center justify-between"><span class="text-gray-600">Location Coverage</span><span class="font-medium">{{ $locationCoverage }}%</span></div>
            <div class="flex items-center justify-between"><span class="text-gray-600">Internal Links</span><span class="font-medium">{{ $internalLinkCoverage }}%</span></div>
            <div class="flex items-center justify-between"><span class="text-gray-600">Competitor Gaps</span><span class="font-medium">{{ $competitorGapCoverage }}%</span></div>
        </div>

        <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-3">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $mode['label'] }}</p>
            <p class="mt-1 text-xs text-gray-600">{{ $mode['description'] }}</p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
