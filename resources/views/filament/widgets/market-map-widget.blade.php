<x-filament-widgets::widget>
    <x-filament::section>
        <div x-data="{ selected: null }">
            <div class="mb-4 flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-cyan-700">Market Map</p>
                    <h3 class="mt-2 text-sm font-semibold uppercase tracking-wide text-gray-500">{{ $domain ?? 'No active site' }}</h3>
                    <p class="mt-1 text-sm text-gray-600">Services × Locations coverage map. Green = covered, red = missing, yellow = opportunity detected.</p>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Service</th>
                            @foreach ($cities as $city)
                                <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $city->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($matrix as $row)
                            <tr>
                                <td class="px-3 py-2 font-medium text-gray-900">{{ $row['service'] }}</td>
                                @foreach ($row['cells'] as $cell)
                                    @php
                                        $bg = $cell['status'] === 'covered'
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : ($cell['status'] === 'opportunity' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700');
                                        $label = $cell['status'] === 'covered' ? 'Covered' : ($cell['status'] === 'opportunity' ? 'Opportunity' : 'Missing');
                                    @endphp
                                    <td class="px-2 py-2 text-center">
                                        <button
                                            type="button"
                                            class="inline-flex rounded-md px-2 py-1 text-xs font-semibold {{ $bg }}"
                                            @click='selected = @json($cell)'
                                        >
                                            {{ $label }}
                                        </button>
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ max(2, $cities->count() + 1) }}" class="px-3 py-8 text-center text-sm text-gray-500">No market map data yet. Run a site scan and coverage sync.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div x-show="selected" x-cloak class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Opportunity details</p>
                <p class="mt-2 text-sm"><span class="font-medium">Service:</span> <span x-text="selected?.service"></span></p>
                <p class="text-sm"><span class="font-medium">Location:</span> <span x-text="selected?.location"></span></p>
                <p class="text-sm"><span class="font-medium">Search volume:</span> <span x-text="selected?.searchVolume || 0"></span></p>
                <p class="text-sm"><span class="font-medium">Competitor evidence:</span> <span x-text="selected?.competitorEvidence || 'N/A'"></span></p>

                <div class="mt-3 flex flex-wrap gap-2">
                    <a :href="selected?.opportunitiesUrl" class="inline-flex items-center rounded-md bg-primary-600 px-3 py-2 text-xs font-semibold text-white">Generate Page</a>
                    <a :href="selected?.opportunitiesUrl" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700">Queue Opportunity</a>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
