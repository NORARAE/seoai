<x-filament-widgets::widget>
    <x-filament::section heading="Top Pages" description="Most visited pages — last {{ $this->days }} days">
        @php $pages = $this->getPages(); @endphp

        @if(empty($pages))
            <p class="text-sm text-gray-500 py-4">No page data available. Connect GA4 to see top pages.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="py-2 pr-4 font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th class="py-2 pr-4 font-semibold text-gray-700 dark:text-gray-300">Page</th>
                            <th class="py-2 pr-4 font-semibold text-gray-700 dark:text-gray-300 text-right">Sessions</th>
                            <th class="py-2 font-semibold text-gray-700 dark:text-gray-300 text-right">Pageviews</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pages as $i => $page)
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="py-2 pr-4 text-gray-400 dark:text-gray-500 tabular-nums">
                                    {{ $i + 1 }}
                                </td>
                                <td class="py-2 pr-4 text-gray-900 dark:text-gray-100 font-mono text-xs break-all">
                                    {{ $page['page'] }}
                                </td>
                                <td class="py-2 pr-4 text-right text-gray-700 dark:text-gray-300 tabular-nums">
                                    {{ number_format($page['sessions']) }}
                                </td>
                                <td class="py-2 text-right text-gray-700 dark:text-gray-300 tabular-nums">
                                    {{ number_format($page['pageviews']) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
