<x-filament-widgets::widget>
    <x-filament::section>
        <p class="text-[11px] font-semibold uppercase tracking-wide text-indigo-700">Estimated Organic Growth Potential</p>
        <h3 class="mt-2 text-sm font-semibold uppercase tracking-wide text-gray-500">{{ $domain ?? 'No active site' }}</h3>

        <p class="mt-4 text-3xl font-semibold text-indigo-700">+{{ number_format($estimatedMonthlyVisits) }} visits/month</p>
        <p class="mt-1 text-sm text-gray-600">Based on opportunities detected during the latest scan.</p>

        <div class="mt-4 rounded-xl border border-gray-200 bg-white p-3">
            <p class="text-xs uppercase tracking-wide text-gray-500">Top Opportunity</p>
            <p class="mt-1 text-sm font-medium text-gray-900">{{ $topKeyword }}</p>
            <p class="mt-1 text-xs text-indigo-700">Est. +{{ number_format($topEstimatedVisits) }} visits/month</p>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <x-filament::button tag="a" size="sm" :href="$opportunitiesUrl">View Opportunities</x-filament::button>
            <x-filament::button tag="a" size="sm" color="success" :href="$generatePagesUrl">Generate Pages</x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
