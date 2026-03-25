<x-filament-widgets::widget>
    <x-filament::section>
        <div class="mb-4 flex items-start justify-between gap-4">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">How This Page Works</p>
                <h3 class="mt-1 text-lg font-semibold text-gray-900">Follow the scan from setup to action</h3>
                <p class="mt-1 text-sm text-gray-600">Each step below shows where the selected site is in the workflow.</p>
            </div>
        </div>
        <div class="flex flex-col gap-4 xl:flex-row xl:items-stretch">
            @foreach ($stages as $stage)
                @php
                    $tone = match ($stage['state']) {
                        'active' => 'border-sky-200 bg-sky-50 text-sky-700',
                        'complete' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                        default => 'border-gray-200 bg-gray-50 text-gray-500',
                    };
                @endphp
                <div class="min-w-0 flex-1 rounded-2xl border p-4 {{ $tone }}">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-[11px] font-semibold uppercase tracking-wide">{{ $stage['label'] }}</p>
                        <p class="text-2xl font-semibold">{{ number_format($stage['count']) }}</p>
                    </div>
                    <p class="mt-2 text-sm leading-5 opacity-90">{{ $stage['description'] }}</p>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>