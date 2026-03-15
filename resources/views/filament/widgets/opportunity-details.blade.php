<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Service</h4>
            <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                {{ $opportunity->service->name }}
            </p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</h4>
            <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                {{ $opportunity->city->name }}, {{ $opportunity->state->code }}
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $opportunity->county->name }} County
            </p>
        </div>
    </div>

    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Opportunity Metrics</h4>
        <dl class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Priority Score</dt>
                <dd class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $opportunity->priority_score }}/100
                </dd>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Traffic Potential</dt>
                <dd class="mt-1 text-2xl font-bold text-warning-600">
                    {{ $opportunity->traffic_potential }}/100
                </dd>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Est. Monthly Searches</dt>
                <dd class="mt-1 text-2xl font-bold text-primary-600">
                    {{ number_format($opportunity->estimated_monthly_searches ?? 0) }}
                </dd>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $opportunity->status_color }}-100 text-{{ $opportunity->status_color }}-800 dark:bg-{{ $opportunity->status_color }}-900 dark:text-{{ $opportunity->status_color }}-200">
                        {{ $opportunity->status_text }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    @if($opportunity->city->population)
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Location Demographics</h4>
        <dl class="space-y-2">
            <div class="flex justify-between">
                <dt class="text-sm text-gray-600 dark:text-gray-400">Population</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ number_format($opportunity->city->population) }}
                </dd>
            </div>
            @if($opportunity->city->lat && $opportunity->city->long)
            <div class="flex justify-between">
                <dt class="text-sm text-gray-600 dark:text-gray-400">Coordinates</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ round($opportunity->city->lat, 4) }}, {{ round($opportunity->city->long, 4) }}
                </dd>
            </div>
            @endif
        </dl>
    </div>
    @endif

    @if($opportunity->last_analyzed_at)
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600 dark:text-gray-400">Last Analyzed</span>
            <span class="text-sm font-medium text-gray-900 dark:text-white">
                {{ $opportunity->last_analyzed_at->diffForHumans() }}
            </span>
        </div>
    </div>
    @endif

    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Why This Opportunity?</h4>
        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
            @if($opportunity->priority_score >= 80)
            <li class="flex items-start">
                <svg class="w-5 h-5 text-success-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>High priority score indicates strong expansion potential</span>
            </li>
            @endif
            @if($opportunity->traffic_potential >= 60)
            <li class="flex items-start">
                <svg class="w-5 h-5 text-success-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Significant traffic potential based on location characteristics</span>
            </li>
            @endif
            @if($opportunity->city->population >= 10000)
            <li class="flex items-start">
                <svg class="w-5 h-5 text-success-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Sizable population provides strong market opportunity</span>
            </li>
            @endif
            <li class="flex items-start">
                <svg class="w-5 h-5 text-success-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>No existing page - clear gap in current coverage</span>
            </li>
        </ul>
    </div>
</div>
