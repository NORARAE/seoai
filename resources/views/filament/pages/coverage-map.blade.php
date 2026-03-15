<x-filament-panels::page>
    {{-- Coverage Stats Summary --}}
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2 lg:grid-cols-4">
        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $stats['total_combinations'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Total Combinations
                </div>
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
                    {{ $stats['coverage_percentage'] ?? 0 }}% coverage
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
            </div>
        </x-filament::section>
    </div>

    {{-- Controls --}}
    <x-filament::section>
        <x-slot name="heading">
            Coverage Matrix
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
            {{-- Filters --}}
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
                </div>
            </div>

            {{-- Legend --}}
            <div class="flex gap-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
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
    </x-filament::section>

    {{-- Opportunities Table --}}
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
                Complete coverage matrix showing all service-location combinations
            @else
                Missing pages ranked by priority score. Higher scores indicate better expansion opportunities.
            @endif
        </x-slot>

        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
