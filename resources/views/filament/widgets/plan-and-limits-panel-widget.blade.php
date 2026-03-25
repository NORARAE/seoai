<x-filament-widgets::widget>
    <x-filament::section>
        <div id="plan-and-limits" class="grid gap-4 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Scan Limits</p>
                <h3 class="mt-2 text-xl font-semibold text-gray-900">
                    {{ $plan?->name ?? 'No active plan detected' }}
                </h3>
                <p class="mt-2 text-sm text-gray-600">
                    @if ($site)
                        These limits control how much of {{ $site->domain }} can be scanned and compared.
                    @else
                        Select a site to see scan limits and competitor scan access.
                    @endif
                </p>

                <div class="mt-5 grid gap-3 md:grid-cols-3">
                    <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Crawl Limit</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($context->limit) }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Free Competitor Scans Used</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($freeScansUsed) }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Paid Scan Credits</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($creditsRemaining) }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-gray-50/70 p-5 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">What To Watch</p>

                @if ($blockedReason === 'crawl_limit')
                    <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-800">
                        <p class="text-sm font-semibold">Discovery is capped by the current crawl limit.</p>
                        <p class="mt-2 text-sm">This site reached its current page allowance. Increase the limit before you expect the next scan to go deeper.</p>
                    </div>
                @elseif ($blockedReason === 'competitor_credit')
                    <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-800">
                        <p class="text-sm font-semibold">Competitor rescans need credits.</p>
                        <p class="mt-2 text-sm">Each competitor receives one free scan. Additional rescans use paid credits.</p>
                    </div>
                @else
                    <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                        <p class="text-sm font-semibold">Your current setup allows the next step.</p>
                        <p class="mt-2 text-sm">There is no current limit blocking another scan or competitor comparison.</p>
                    </div>
                @endif

                <div class="mt-4 space-y-2 text-sm text-gray-600">
                    <p>Subscription status: {{ $subscription?->status ?? 'none' }}</p>
                    <p>Queued + processing URLs: {{ number_format($context->queued + $context->processing) }}</p>
                    <p>Usage snapshot entries: {{ is_countable($usage) ? count($usage) : 0 }}</p>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>