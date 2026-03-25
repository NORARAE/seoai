<x-filament-widgets::widget>
    <x-filament::section>
        @php
            $stateTone = match ($context->state) {
                'complete' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
                'scanning' => 'text-sky-700 bg-sky-50 border-sky-200',
                'stalled' => 'text-rose-700 bg-rose-50 border-rose-200',
                'limited' => 'text-amber-700 bg-amber-50 border-amber-200',
                default => 'text-gray-700 bg-gray-50 border-gray-200',
            };
            $stateLabel = match ($context->state) {
                'complete' => 'Snapshot ready',
                'scanning' => 'Scan running',
                'stalled' => 'Scan needs attention',
                'limited' => 'Paused by scan limit',
                default => 'No scan yet',
            };
        @endphp

        <div class="grid gap-4 xl:grid-cols-[1.5fr_1fr]">
            <div class="rounded-2xl border border-gray-200/90 bg-white/95 p-6 shadow-md">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide {{ $stateTone }}">
                                {{ $stateLabel }}
                            </span>
                            @if ($context->scanRunId())
                                <span class="text-xs text-gray-500">Metrics scan #{{ $context->scanRunId() }}</span>
                            @endif
                        </div>

                        <h3 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">
                            {{ $activeSite?->domain ?? 'No active site selected' }}
                        </h3>

                        <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-600">
                            @if (! $activeSite && $siteCount <= 0)
                                No sites are connected yet. Add your first site to start scanning, monitor progress, and uncover opportunities.
                            @elseif (! $activeSite)
                                Select a site to start working. Once a site is active, you can run a scan, monitor progress, and review the pages and opportunities tied to that site.
                            @elseif ($context->state === 'scanning')
                                A scan is running now. Progress updates will appear below, while completed metrics stay pinned to the latest finished snapshot until this scan completes.
                            @elseif ($context->state === 'stalled')
                                This scan stopped making progress before it finished. Resume it to continue discovering and analyzing pages for this site.
                            @elseif ($context->state === 'limited')
                                This site reached its current scan limit. Review limits below before expecting the next scan to go deeper.
                            @elseif ($context->hasMetricsScan())
                                You are viewing the latest completed snapshot for this site, including discovered pages, page health signals, and recommended opportunities.
                            @else
                                This site is connected, but it does not have a completed scan yet. Start a scan to create the first usable snapshot for page review and opportunities.
                            @endif
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2 lg:justify-end">
                        @if ($heroPrimaryAction)
                            @if ($heroPrimaryAction['type'] === 'method')
                                <x-filament::button wire:click="{{ $heroPrimaryAction['value'] }}" wire:loading.attr="disabled" wire:target="{{ $heroPrimaryAction['value'] }}" color="{{ $heroPrimaryAction['color'] }}">
                                    <span wire:loading.remove wire:target="{{ $heroPrimaryAction['value'] }}">{{ $heroPrimaryAction['label'] }}</span>
                                    <span wire:loading wire:target="{{ $heroPrimaryAction['value'] }}">Working...</span>
                                </x-filament::button>
                            @else
                                <x-filament::button tag="a" color="{{ $heroPrimaryAction['color'] }}" :href="$heroPrimaryAction['value']">{{ $heroPrimaryAction['label'] }}</x-filament::button>
                            @endif
                        @endif

                        @foreach ($heroSecondaryActions as $action)
                            <x-filament::button tag="a" size="sm" color="{{ $action['color'] }}" :href="$action['value']">{{ $action['label'] }}</x-filament::button>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 rounded-2xl border border-sky-200 bg-sky-50 px-4 py-5 text-sky-900">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-sky-700">What happens next</p>
                    <h4 class="mt-2 text-lg font-semibold">{{ $nextStep['title'] }}</h4>
                    <p class="mt-2 text-sm leading-6">{{ $nextStep['body'] }}</p>
                    <div class="mt-3">
                        @if ($nextStep['actionType'] === 'method')
                            <x-filament::button size="sm" color="primary" wire:click="{{ $nextStep['actionValue'] }}" wire:loading.attr="disabled" wire:target="{{ $nextStep['actionValue'] }}">
                                {{ $nextStep['actionLabel'] }}
                            </x-filament::button>
                        @elseif ($nextStep['actionType'] === 'url')
                            <x-filament::button tag="a" size="sm" color="primary" :href="$nextStep['actionValue']">{{ $nextStep['actionLabel'] }}</x-filament::button>
                        @else
                            <x-filament::button tag="a" size="sm" color="primary" :href="$nextStep['actionValue']">{{ $nextStep['actionLabel'] }}</x-filament::button>
                        @endif
                    </div>
                </div>

                <div class="mt-6 grid gap-3 md:grid-cols-4">
                    <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Pages Found</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($context->discovered) }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Pages Analyzed</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($context->crawled) }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Opportunities Found</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($context->opportunities) }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Pages Still Running</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($context->queued + $context->processing) }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-gray-50/60 p-5 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Active Site</p>
                <h4 class="mt-2 text-lg font-semibold text-gray-900">
                    @if ($siteCount === 1)
                        1 accessible site
                    @else
                        {{ number_format($siteCount) }} accessible sites
                    @endif
                </h4>
                <p class="mt-2 text-sm leading-6 text-gray-600">{{ $siteSelectorHelp }}</p>

                <div class="mt-4 grid gap-2">
                    @forelse ($sites as $site)
                        <button type="button" wire:click="selectSite({{ $site->id }})" class="flex items-center justify-between rounded-xl border px-3 py-3 text-left transition {{ $selectedSiteId === $site->id ? 'border-sky-300 bg-sky-50 text-sky-900' : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300' }}">
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-semibold">{{ $site->domain }}</span>
                                <span class="mt-0.5 block text-xs {{ $selectedSiteId === $site->id ? 'text-sky-700' : 'text-gray-500' }}">
                                    {{ $selectedSiteId === $site->id ? 'Currently selected' : 'Switch this page to this site' }}
                                </span>
                            </span>
                            @if ($selectedSiteId === $site->id)
                                <span class="rounded-full bg-sky-100 px-2 py-1 text-[11px] font-semibold text-sky-700">Active</span>
                            @endif
                        </button>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 bg-white p-4 text-sm text-gray-600">
                            No sites are connected yet. Add your first site to start scanning and uncover opportunities.
                        </div>
                    @endforelse
                </div>

                <div class="mt-4 space-y-2 text-sm text-gray-600">
                    <p>Client link: {{ $resolvedClientId ? 'connected' : 'missing' }}</p>
                    <p>
                        @if ($context->lastCompletedAt)
                            Last completed scan {{ $context->lastCompletedAt->diffForHumans() }}.
                        @else
                            No completed scan recorded yet.
                        @endif
                    </p>
                    <p>
                        @if ($context->lastActivityAt)
                            Last scan activity {{ $context->lastActivityAt->diffForHumans() }}.
                        @else
                            No scan activity recorded yet.
                        @endif
                    </p>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <x-filament::button tag="a" size="sm" color="primary" :href="$addSiteUrl">Add Site</x-filament::button>
                    <x-filament::button tag="a" size="sm" color="gray" :href="$sitesUrl">Manage Sites</x-filament::button>
                    @if ($showSidebarScanProgress)
                        <x-filament::button tag="a" size="sm" color="gray" :href="$crawlQueueUrl">Open Scan Progress</x-filament::button>
                    @endif
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
