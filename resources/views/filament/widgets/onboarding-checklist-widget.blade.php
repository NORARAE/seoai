<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-semibold text-gray-100">First-Run Checklist</h3>
                    <span class="inline-flex items-center rounded-full bg-sky-900/40 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-sky-200">Launch Sequence</span>
                </div>
                <p class="mt-1 text-sm text-gray-300">Move from crawl setup to review-ready growth actions with the smallest possible operator loop.</p>
            </div>
            <x-filament::button size="sm" color="gray" wire:click="dismissOnboarding">
                Dismiss Permanently
            </x-filament::button>
        </div>

        <ol class="mt-5 grid gap-3 text-sm text-gray-300 sm:grid-cols-2">
            <li class="rounded-xl border border-gray-700 bg-gray-900/70 p-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-sky-300">Step 1</p>
                <p class="mt-2 font-medium text-gray-100">Add your website</p>
                <p class="mt-1 text-xs text-gray-300">Choose your active site or enter a new domain at the top of the command center.</p>
                <a href="/admin" class="mt-2 inline-flex text-xs font-semibold text-sky-300 hover:text-sky-200">Go to dashboard</a>
            </li>
            <li class="rounded-xl border border-gray-700 bg-gray-900/70 p-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-amber-300">Step 2</p>
                <p class="mt-2 font-medium text-gray-100">Run your first scan</p>
                <p class="mt-1 text-xs text-gray-300">Use Start Site Scan to queue discovery and begin collecting pages and links.</p>
                <a href="/admin" class="mt-2 inline-flex text-xs font-semibold text-sky-300 hover:text-sky-200">Run scan</a>
            </li>
            <li class="rounded-xl border border-gray-700 bg-gray-900/70 p-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-indigo-300">Step 3</p>
                <p class="mt-2 font-medium text-gray-100">Review opportunities</p>
                <p class="mt-1 text-xs text-gray-300">Approve the highest-impact opportunities before sending anything to generation.</p>
                <a href="/admin/seo-opportunities" class="mt-2 inline-flex text-xs font-semibold text-sky-300 hover:text-sky-200">Open opportunities</a>
            </li>
            <li class="rounded-xl border border-gray-700 bg-gray-900/70 p-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-300">Step 4</p>
                <p class="mt-2 font-medium text-gray-100">Generate pages</p>
                <p class="mt-1 text-xs text-gray-300">Generate payloads from approved opportunities and send them into editorial review.</p>
                <a href="/admin/seo-opportunities" class="mt-2 inline-flex text-xs font-semibold text-sky-300 hover:text-sky-200">Generate from opportunities</a>
            </li>
            <li class="rounded-xl border border-gray-700 bg-gray-900/70 p-4 shadow-sm sm:col-span-2">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-cyan-300">Step 5</p>
                <p class="mt-2 font-medium text-gray-100">Publish content</p>
                <p class="mt-1 text-xs text-gray-300">Review generated payloads, approve final edits, and publish through your editorial workflow.</p>
                <a href="/admin/page-payloads" class="mt-2 inline-flex text-xs font-semibold text-sky-300 hover:text-sky-200">Open payload workflow</a>
            </li>
        </ol>

        <div class="mt-4 rounded-xl border border-gray-700 bg-gray-900/60 px-4 py-3">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-300">Operator note</p>
            <p class="mt-1 text-sm text-gray-300">If you are onboarding a second site, repeat this sequence with a clear site selection pattern before running generation at scale.</p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
