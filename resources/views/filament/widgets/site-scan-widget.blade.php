<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid gap-4 lg:grid-cols-[1.2fr_1fr]">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-sky-700">Dashboard Context</p>
                <h3 class="mt-2 text-lg font-semibold text-gray-900">Active Site: {{ $activeSite?->domain ?? 'No site selected' }}</h3>
                <p class="mt-1 text-sm text-gray-600">Pick the site this dashboard should use. All widgets and actions follow this active site context.</p>

                <div class="mt-3">
                    <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Select Active Site</label>
                    <select
                        wire:model.live="selectedSiteId"
                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    >
                        @forelse ($sites as $site)
                            <option value="{{ $site->id }}">{{ $site->domain }}</option>
                        @empty
                            <option value="">No sites available</option>
                        @endforelse
                    </select>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-600">
                    <span>Client context: {{ $resolvedClientId ? 'linked' : 'not linked' }}</span>
                    <a href="{{ $clientsUrl }}" class="font-medium text-sky-700 hover:text-sky-800">Open Clients</a>
                    <a href="{{ $sitesUrl }}" class="font-medium text-sky-700 hover:text-sky-800">Open Sites</a>
                </div>

                @if (! $resolvedClientId)
                    <p class="mt-2 text-xs text-amber-700">No client is linked to this admin user yet, so site access falls back to existing site ownership and assignments.</p>
                @endif
            </div>

            <div class="rounded-xl border border-primary-200 bg-primary-50/50 p-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-primary-700">First Action</p>
                <h3 class="mt-2 text-lg font-semibold text-gray-900">Scan a Website</h3>
                <p class="mt-1 text-sm text-gray-600">Start a scan for the currently selected Active Site.</p>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <x-filament::button wire:click="startSiteScan" color="primary" wire:loading.attr="disabled" wire:target="startSiteScan">
                        <span wire:loading.remove wire:target="startSiteScan">Start Site Scan</span>
                        <span wire:loading wire:target="startSiteScan">Queuing Scan...</span>
                    </x-filament::button>
                    <p class="text-xs text-gray-600">Uses the selected Active Site.</p>
                </div>
                <p class="mt-2 text-xs text-gray-500">No additional domain entry is required on this screen.</p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
