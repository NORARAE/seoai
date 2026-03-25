<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">How To Use The Workspace</x-slot>
            <x-slot name="description">Start in Command Center, keep one site active, and move from scan progress to page review to opportunity review.</x-slot>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-sky-700">Step 1</p>
                    <h3 class="mt-2 text-base font-semibold text-gray-900">Select the site you are working on</h3>
                    <p class="mt-2 text-sm text-gray-700">Use the site selector in Command Center to keep the workspace focused on one site at a time.</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-amber-700">Step 2</p>
                    <h3 class="mt-2 text-base font-semibold text-gray-900">Start or resume a scan</h3>
                    <p class="mt-2 text-sm text-gray-700">Run the first scan to create a usable site snapshot. If progress stops, resume the scan before reviewing downstream results.</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-indigo-700">Step 3</p>
                    <h3 class="mt-2 text-base font-semibold text-gray-900">Check scan progress</h3>
                    <p class="mt-2 text-sm text-gray-700">Use Scan Progress to confirm whether work is queued, processing, stalled, or failed.</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-700">Step 4</p>
                    <h3 class="mt-2 text-base font-semibold text-gray-900">Review discovered pages</h3>
                    <p class="mt-2 text-sm text-gray-700">Once a scan finishes, confirm what the platform found and how pages were classified.</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-rose-700">Step 5</p>
                    <h3 class="mt-2 text-base font-semibold text-gray-900">Review SEO opportunities</h3>
                    <p class="mt-2 text-sm text-gray-700">Move into SEO Opportunities to decide what should be created, improved, or fixed first.</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-violet-700">Step 6</p>
                    <h3 class="mt-2 text-base font-semibold text-gray-900">Generate and publish after review</h3>
                    <p class="mt-2 text-sm text-gray-700">Use generation and publishing tools after page and opportunity review, not as the first stop.</p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">When A Page Looks Empty</x-slot>
            <div class="grid gap-3 md:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-sm font-medium text-gray-900">No active site selected</p>
                    <p class="mt-1 text-xs text-gray-600">Return to Command Center and choose the site you want the workspace to follow.</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-sm font-medium text-gray-900">The selected snapshot has no rows</p>
                    <p class="mt-1 text-xs text-gray-600">Open Scan History to inspect another snapshot, or return to Command Center to start a fresh scan.</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-sm font-medium text-gray-900">The current scan is still running</p>
                    <p class="mt-1 text-xs text-gray-600">Check Scan Progress first. Some downstream pages stay pinned to the latest completed snapshot until the current scan finishes.</p>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
