<x-filament-widgets::widget>
    <x-filament::section>
        @if ($hasRequestedCurrentScan && ! $hasResolvedCurrentScan)
            <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
                <p class="text-sm font-semibold">The requested site or snapshot is not available.</p>
                <p class="mt-1 text-sm">This page stays empty instead of showing unrelated results.</p>
            </div>
        @endif

        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Current View</p>
                <h3 class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $context->site?->domain ?? 'No site selected' }}
                </h3>
                <p class="mt-1 text-sm text-gray-600">
                    @if ($hasRequestedCurrentScan && ! $hasResolvedCurrentScan)
                        The requested site or snapshot is not available in your current context.
                    @elseif ($context->scanRunId())
                        You are viewing the selected snapshot for this site.
                    @else
                        You are viewing site-wide history for this page.
                    @endif
                </p>
                @if ($competitorDomain || $competitorScanRun)
                    <p class="mt-1 text-sm text-gray-600">
                        Competitor comparison:
                        {{ $competitorDomain?->domain ?? 'saved competitor' }}
                        @if ($competitorScanRun)
                            using competitor scan #{{ $competitorScanRun->id }}
                        @endif
                        @if ($context->scanRunId())
                            against site scan #{{ $context->scanRunId() }}
                        @endif
                        .
                    </p>
                @endif
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Discovered Pages</p>
                    <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($context->discovered) }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Analyzed Pages</p>
                    <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($context->crawled) }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Opportunities</p>
                    <p class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($context->opportunities) }}</p>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>