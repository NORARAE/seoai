<x-filament-widgets::widget>
    <x-filament::section heading="Traffic Sources" description="Top channels driving sessions — last {{ $this->days }} days">
        @php $sources = $this->getSources(); @endphp

        @if(empty($sources))
            <p class="text-sm text-gray-500 py-4">No traffic source data available. Connect GA4 to see channel breakdown.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="py-2 pr-4 font-semibold text-gray-700 dark:text-gray-300">Source / Medium</th>
                            <th class="py-2 pr-4 font-semibold text-gray-700 dark:text-gray-300 text-right">Sessions</th>
                            <th class="py-2 pr-4 font-semibold text-gray-700 dark:text-gray-300 text-right">Users</th>
                            <th class="py-2 pr-4 font-semibold text-gray-700 dark:text-gray-300 text-right">Pageviews</th>
                            <th class="py-2 font-semibold text-gray-700 dark:text-gray-300 text-right">Bounce Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sources as $source)
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="py-2 pr-4 text-gray-900 dark:text-gray-100 font-medium">
                                    {{ $source['source'] }}
                                </td>
                                <td class="py-2 pr-4 text-right text-gray-700 dark:text-gray-300">
                                    {{ number_format($source['sessions']) }}
                                </td>
                                <td class="py-2 pr-4 text-right text-gray-700 dark:text-gray-300">
                                    {{ number_format($source['users']) }}
                                </td>
                                <td class="py-2 pr-4 text-right text-gray-700 dark:text-gray-300">
                                    {{ number_format($source['pageviews']) }}
                                </td>
                                <td class="py-2 text-right">
                                    <span class="{{ $source['bounce_rate'] <= 50 ? 'text-green-600' : 'text-yellow-600' }}">
                                        {{ $source['bounce_rate'] }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
