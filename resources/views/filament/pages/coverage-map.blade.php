<x-filament-panels::page>
    <div class="mb-6 grid gap-4 xl:grid-cols-[minmax(0,1fr)_320px]">
        <x-filament::section>
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-sky-700">Coverage Intelligence</p>
                    <h2 class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">See where service demand exists before content gets generated.</h2>
                    <p class="mt-2 max-w-3xl text-sm text-gray-600 dark:text-gray-400">Use this matrix to understand existing coverage, missing combinations, and where generation should happen next. The goal is to make expansion decisions visible before they turn into queue volume.</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Current mode</p>
                    <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $showAll ? 'Full matrix view' : 'Opportunity-ranked view' }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $showAll ? 'Showing existing and missing combinations together.' : 'Focusing on missing combinations with the highest expansion value.' }}</p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Decision Guide</x-slot>
            <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                <p>Green means the footprint already exists.</p>
                <p>Amber marks pages with weaker performance signals.</p>
                <p>Red indicates missing pages that can become growth candidates.</p>
                <p>Gray is incomplete analysis or unclassified inventory.</p>
            </div>
        </x-filament::section>
    </div>

    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2 lg:grid-cols-4">
        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $stats['total_combinations'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Total Combinations
                </div>
                <div class="text-xs text-gray-500 mt-1">Available service and location pairings in scope</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-success-600">
                    {{ $stats['pages_exist'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Pages Created
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    {{ $stats['coverage_percentage'] ?? 0 }}% of addressable footprint covered
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-danger-600">
                    {{ $stats['pages_missing'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Missing Pages
                </div>
                <div class="text-xs text-gray-500 mt-1">Net-new coverage still open for expansion</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-warning-600">
                    {{ $stats['high_priority_gaps'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    High Priority Gaps
                </div>
                <div class="text-xs text-gray-500 mt-1">The strongest near-term page creation candidates</div>
            </div>
        </x-filament::section>
    </div>

    <x-filament::section>
        <x-slot name="heading">
            Coverage Matrix
        </x-slot>

        <x-slot name="description">
            Tune the scope, refresh the matrix, and batch-generate only when the current state and service context look correct.
        </x-slot>

        <x-slot name="headerEnd">
            <div class="flex gap-2">
                <x-filament::button
                    wire:click="refreshMatrix"
                    color="gray"
                    size="sm"
                >
                    <x-filament::icon
                        icon="heroicon-o-arrow-path"
                        class="w-4 h-4 mr-1"
                    />
                    Refresh Matrix
                </x-filament::button>

                <x-filament::button
                    wire:click="generateBatch(10)"
                    color="primary"
                    size="sm"
                >
                    <x-filament::icon
                        icon="heroicon-o-plus-circle"
                        class="w-4 h-4 mr-1"
                    />
                    Generate Top 10
                </x-filament::button>
            </div>
        </x-slot>

        <div class="space-y-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-200">
                        State
                    </label>
                    <select
                        wire:model.live="selectedState.id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">Select State</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-200">
                        Service (optional)
                    </label>
                    <select
                        wire:model.live="selectedService.id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">All Services</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-200">
                        View Mode
                    </label>
                    <div class="mt-1 flex items-center gap-2">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                wire:model.live="showAll"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Show all (incl. existing)
                            </span>
                        </label>
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Leave this off when you want the map to surface only creation opportunities.</p>
                </div>
            </div>

            <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Legend</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Interpret the matrix before generating new pages.</p>
                </div>
                <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-success-500"></div>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Active (good traffic)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-warning-500"></div>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Low Traffic</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-danger-500"></div>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Missing Page</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-gray-300 dark:bg-gray-600"></div>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Pending Analysis</span>
                </div>
                </div>
            </div>
        </div>
    </x-filament::section>

    <x-filament::section class="mt-6">
        <x-slot name="heading">
            @if($showAll)
                All Service Locations
            @else
                Top Expansion Opportunities
            @endif
        </x-slot>

        <x-slot name="description">
            @if($showAll)
                Complete coverage matrix showing every known service-location combination in the selected scope.
            @else
                Missing pages ranked by priority score so the strongest expansion opportunities rise to the top first.
            @endif
        </x-slot>

        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
