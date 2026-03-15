<div class="filament-widget">
    @php
        $usageData = $this->getUsageData();
        $clientData = $this->getClientData();
    @endphp

    @if($usageData && $clientData)
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Usage & Limits
                </h3>
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $clientData['on_trial'] ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                    {{ $clientData['plan'] }}
                    @if($clientData['on_trial'])
                        (Trial)
                    @endif
                </span>
            </div>

            @if($clientData['on_trial'] && $clientData['trial_ends_at'])
                <div class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        Trial ends {{ $clientData['trial_ends_at']->diffForHumans() }}
                    </p>
                </div>
            @endif

            <div class="space-y-4">
                @foreach(['sites', 'pages', 'ai_operations'] as $resource)
                    @php
                        $data = $usageData[$resource];
                        $label = ucfirst(str_replace('_', ' ', $resource));
                    @endphp

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $label }}
                            </span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $data['used'] }} / {{ $data['limit'] }}
                            </span>
                        </div>

                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div 
                                class="h-2.5 rounded-full {{ $data['exceeded'] ? 'bg-red-600' : ($data['percentage'] > 80 ? 'bg-yellow-500' : 'bg-blue-600') }}"
                                style="width: {{ min(100, $data['percentage']) }}%"
                            ></div>
                        </div>

                        @if($data['exceeded'])
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                                Limit exceeded
                            </p>
                        @elseif($data['percentage'] > 80)
                            <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-400">
                                {{ $data['remaining'] }} remaining
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
