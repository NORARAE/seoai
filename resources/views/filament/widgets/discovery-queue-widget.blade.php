<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Scan Progress</h3>
                    <span class="inline-flex items-center rounded-full border border-gray-200 px-2 py-0.5 text-[10px] font-medium uppercase tracking-wide text-gray-500 dark:border-gray-700 dark:text-gray-300">Live Scan</span>
                </div>
                @if ($activeSiteDomain)
                    <p class="mt-1 break-all text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">for {{ $activeSiteDomain }}</p>
                @endif
                <p class="mt-2 max-w-3xl text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                    @if ($context->activeScanRunId())
                        These pages are still being scanned right now. Use this panel when you need to restart progress or refresh the opportunity review.
                    @else
                        No site scan is running right now. Start a scan above to begin finding and analyzing pages.
                    @endif
                </p>
            </div>
            <x-filament::button tag="a" size="sm" color="gray" :href="$crawlQueueUrl" class="self-start md:self-auto">
                Open Scan Progress
            </x-filament::button>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="h-full rounded-xl border border-gray-200/90 bg-white/90 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900/80">
                <p class="text-[11px] font-medium uppercase tracking-[0.08em] text-gray-500 dark:text-gray-400">Queued</p>
                <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-gray-900 dark:text-white">{{ number_format($queued) }}</p>
                <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">Waiting to be scanned</p>
            </div>

            <div class="h-full rounded-xl border border-primary-200/70 bg-primary-50/30 p-4 shadow-sm dark:border-primary-900/50 dark:bg-primary-950/20">
                <p class="text-[11px] font-medium uppercase tracking-[0.08em] text-primary-700 dark:text-primary-300">Processing</p>
                <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-primary-700 dark:text-primary-300">{{ number_format($processing) }}</p>
                <p class="mt-1 text-[11px] text-primary-700/80 dark:text-primary-300/80">Being scanned now</p>
            </div>

            <div class="h-full rounded-xl border border-danger-200/70 bg-danger-50/35 p-4 shadow-sm dark:border-danger-900/50 dark:bg-danger-950/20">
                <p class="text-[11px] font-medium uppercase tracking-[0.08em] text-danger-700 dark:text-danger-300">Failed</p>
                <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-danger-700 dark:text-danger-300">{{ number_format($failed) }}</p>
                <p class="mt-1 text-[11px] text-danger-700/80 dark:text-danger-300/80">Need another try</p>
            </div>
        </div>

        <div class="mt-5 rounded-xl border {{ $isStalled ? 'border-rose-200 bg-rose-50 text-rose-800' : 'border-gray-200 bg-gray-50/70 text-gray-700' }} px-4 py-5">
            <p class="text-sm font-semibold">
                @if ($isStalled)
                    Scan progress appears stalled.
                @elseif ($lastActivityAt)
                    Last scan activity {{ $lastActivityAt->diffForHumans() }}.
                @else
                    No live scan activity recorded yet.
                @endif
            </p>
            <p class="mt-1 text-sm opacity-90">
                @if ($isStalled)
                    Pages are waiting, but progress has stopped. Resume or dispatch the scan to keep moving.
                @elseif ($context->activeScanRunId())
                    Use these actions if you need to keep the scan moving or refresh what the team should review next.
                @else
                    Start a site scan when you want the system to find pages and create fresh opportunities.
                @endif
            </p>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-2 border-t border-gray-200 pt-4 dark:border-gray-700/60">
            @if ($showStartScan)
                <x-filament::button size="sm" color="primary" wire:click="startCrawl">
                    Start Scan
                </x-filament::button>
            @endif
            @if ($showRetryProgress)
                <x-filament::button size="sm" color="gray" wire:click="dispatchQueue">
                    Retry Scan Progress
                </x-filament::button>
            @endif
            @if ($showRefreshOpportunities)
                <x-filament::button size="sm" color="success" wire:click="syncOpportunities">
                    Refresh Opportunity Review
                </x-filament::button>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
