<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Funnel Visibility and Drop-Off</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">One-screen view of progression leaks and score-band conversion behavior.</p>
            </div>
        </div>

        @if (! $hasAccess)
            <div class="mt-4 rounded-lg border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-800 dark:border-danger-800 dark:bg-danger-950/30 dark:text-danger-300">
                You do not have permission to view this report.
            </div>
        @else
            <div class="mt-5 overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Stage</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Count</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Drop From Previous</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Retention</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-gray-900/40">
                        @foreach ($progressionRows as $row)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $row['stage'] }}</td>
                                <td class="px-4 py-3 text-right tabular-nums text-gray-900 dark:text-white">{{ number_format($row['count']) }}</td>
                                <td class="px-4 py-3 text-right tabular-nums {{ $row['drop_count'] > 0 ? 'text-danger-700 dark:text-danger-300' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ number_format($row['drop_count']) }}
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums text-gray-700 dark:text-gray-300">
                                    {{ $row['retention'] === null ? '—' : $row['retention'] . '%' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Score Band</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Entered (Result)</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Clicked Upgrade</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Stalled</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Dropped at Checkout</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Converted</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Stall %</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Convert %</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-gray-900/40">
                        @foreach ($scoreBandRows as $row)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $row['band'] }}</td>
                                <td class="px-4 py-3 text-right tabular-nums text-gray-900 dark:text-white">{{ number_format($row['entered']) }}</td>
                                <td class="px-4 py-3 text-right tabular-nums text-gray-900 dark:text-white">{{ number_format($row['upgraded']) }}</td>
                                <td class="px-4 py-3 text-right tabular-nums text-warning-700 dark:text-warning-300">{{ number_format($row['stalled']) }}</td>
                                <td class="px-4 py-3 text-right tabular-nums text-danger-700 dark:text-danger-300">{{ number_format($row['dropped']) }}</td>
                                <td class="px-4 py-3 text-right tabular-nums text-success-700 dark:text-success-300">{{ number_format($row['converted']) }}</td>
                                <td class="px-4 py-3 text-right tabular-nums text-gray-700 dark:text-gray-300">
                                    {{ $row['stall_rate'] === null ? '—' : $row['stall_rate'] . '%' }}
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums text-gray-700 dark:text-gray-300">
                                    {{ $row['convert_rate'] === null ? '—' : $row['convert_rate'] . '%' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 rounded-xl border border-primary-200/70 bg-primary-50/40 px-4 py-4 dark:border-primary-900/50 dark:bg-primary-950/20">
                <p class="text-xs font-semibold uppercase tracking-[0.08em] text-primary-700 dark:text-primary-300">Actionable Interpretation</p>
                <ul class="mt-2 space-y-1 text-sm leading-relaxed text-gray-700 dark:text-gray-200">
                    @foreach ($insights as $insight)
                        <li>{{ $insight }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>