<div class="space-y-4">
    {{-- Header Info --}}
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
                {{ $opportunity->location->name }}, {{ $opportunity->location->state->code }}
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $opportunity->location->county->name }} County
            </p>
        </div>
    </div>

    {{-- Revenue Metrics --}}
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Revenue Potential</h4>
        <dl class="grid grid-cols-2 gap-4">
            <div class="bg-success-50 dark:bg-success-900/20 p-4 rounded-lg border border-success-200 dark:border-success-800">
                <dt class="text-xs font-medium text-success-700 dark:text-success-400">Est. Monthly Revenue</dt>
                <dd class="mt-1 text-3xl font-bold text-success-900 dark:text-success-100">
                    ${{ number_format($opportunity->estimated_monthly_revenue, 0) }}
                </dd>
                <p class="text-xs text-success-600 dark:text-success-400 mt-1">
                    Based on {{ $opportunity->search_volume }} searches/mo
                </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Priority Score</dt>
                <dd class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">
                    {{ number_format($opportunity->priority_score, 0) }}/100
                </dd>
            </div>
        </dl>
    </div>

    {{-- SEO Metrics --}}
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">SEO Metrics</h4>
        <dl class="grid grid-cols-3 gap-4">
            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Search Volume</dt>
                <dd class="mt-1 text-xl font-bold text-primary-600 dark:text-primary-400">
                    {{ number_format($opportunity->search_volume) }}
                </dd>
                <p class="text-xs text-gray-500 mt-1">searches/month</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Rank Potential</dt>
                <dd class="mt-1 text-xl font-bold text-warning-600 dark:text-warning-400">
                    {{ number_format($opportunity->rank_potential, 0) }}%
                </dd>
                <p class="text-xs text-gray-500 mt-1">
                    @if($opportunity->rank_potential >= 75)
                        Excellent
                    @elseif($opportunity->rank_potential >= 50)
                        Good
                    @else
                        Moderate
                    @endif
                </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Competition</dt>
                <dd class="mt-1 text-xl font-bold" style="color: {{ $opportunity->competition_score >= 70 ? 'rgb(239 68 68)' : ($opportunity->competition_score >= 50 ? 'rgb(245 158 11)' : 'rgb(34 197 94)') }}">
                    {{ number_format($opportunity->competition_score, 0) }}/100
                </dd>
                <p class="text-xs text-gray-500 mt-1">
                    @if($opportunity->competition_score >= 70)
                        High
                    @elseif($opportunity->competition_score >= 50)
                        Medium
                    @else
                        Low
                    @endif
                </p>
            </div>
        </dl>
    </div>

    {{-- Opportunity Type --}}
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Opportunity Classification</h4>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" 
                  style="background-color: {{ $opportunity->opportunity_type === 'quick_win' ? 'rgb(220 252 231)' : ($opportunity->opportunity_type === 'high_volume' ? 'rgb(219 234 254)' : 'rgb(243 244 246)') }}; 
                         color: {{ $opportunity->opportunity_type === 'quick_win' ? 'rgb(22 101 52)' : ($opportunity->opportunity_type === 'high_volume' ? 'rgb(30 64 175)' : 'rgb(55 65 81)') }}">
                {{ str_replace('_', ' ', ucwords($opportunity->opportunity_type, '_')) }}
            </span>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
            @if($opportunity->opportunity_type === 'quick_win')
                ⚡ Low competition with high rank potential - fast results expected
            @elseif($opportunity->opportunity_type === 'high_volume')
                📈 High search volume opportunity - significant traffic potential
            @elseif($opportunity->opportunity_type === 'new_page')
                ✨ New page opportunity - expand coverage
            @elseif($opportunity->opportunity_type === 'underperforming')
                ⚠️ Existing page ranking poorly - optimization needed
            @elseif($opportunity->opportunity_type === 'content_gap')
                🎯 Content gap identified - competitor coverage present
            @endif
        </p>
    </div>

    @if($opportunity->page_exists)
    {{-- Current Performance --}}
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Current Performance</h4>
        <dl class="grid grid-cols-4 gap-3">
            @if($opportunity->current_position)
            <div class="bg-gray-50 dark:bg-gray-800 p-2 rounded">
                <dt class="text-xs text-gray-500">Position</dt>
                <dd class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($opportunity->current_position, 1) }}</dd>
            </div>
            @endif
            @if($opportunity->current_impressions)
            <div class="bg-gray-50 dark:bg-gray-800 p-2 rounded">
                <dt class="text-xs text-gray-500">Impressions</dt>
                <dd class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($opportunity->current_impressions) }}</dd>
            </div>
            @endif
            @if($opportunity->current_clicks)
            <div class="bg-gray-50 dark:bg-gray-800 p-2 rounded">
                <dt class="text-xs text-gray-500">Clicks</dt>
                <dd class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($opportunity->current_clicks) }}</dd>
            </div>
            @endif
            @if($opportunity->current_ctr)
            <div class="bg-gray-50 dark:bg-gray-800 p-2 rounded">
                <dt class="text-xs text-gray-500">CTR</dt>
                <dd class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($opportunity->current_ctr * 100, 2) }}%</dd>
            </div>
            @endif
        </dl>
    </div>
    @endif

    {{-- Revenue Calculation Breakdown --}}
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Revenue Calculation</h4>
        <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg text-sm space-y-1">
            <div class="flex justify-between">
                <span class="text-blue-700 dark:text-blue-300">Monthly Searches:</span>
                <span class="font-medium text-blue-900 dark:text-blue-100">{{ number_format($opportunity->search_volume) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-blue-700 dark:text-blue-300">Estimated CTR:</span>
                <span class="font-medium text-blue-900 dark:text-blue-100">
                    {{ $opportunity->rank_potential >= 90 ? '31.8%' : ($opportunity->rank_potential >= 80 ? '15.8%' : ($opportunity->rank_potential >= 70 ? '11.0%' : '8.2%')) }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-blue-700 dark:text-blue-300">Conversion Rate:</span>
                <span class="font-medium text-blue-900 dark:text-blue-100">{{ number_format($opportunity->conversion_rate * 100, 2) }}%</span>
            </div>
            <div class="flex justify-between">
                <span class="text-blue-700 dark:text-blue-300">Service Value:</span>
                <span class="font-medium text-blue-900 dark:text-blue-100">${{ number_format($opportunity->service_value, 2) }}</span>
            </div>
            <div class="border-t border-blue-200 dark:border-blue-800 mt-2 pt-2 flex justify-between">
                <span class="font-medium text-blue-900 dark:text-blue-100">Est. Monthly Revenue:</span>
                <span class="font-bold text-lg text-blue-900 dark:text-blue-100">${{ number_format($opportunity->estimated_monthly_revenue, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Why This Opportunity --}}
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Why This Opportunity?</h4>
        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
            @if($opportunity->priority_score >= 80)
            <li class="flex items-start">
                <svg class="w-5 h-5 text-success-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span><strong>High Priority:</strong> Top-ranked opportunity for revenue generation</span>
            </li>
            @endif
            @if($opportunity->search_volume >= 100)
            <li class="flex items-start">
                <svg class="w-5 h-5 text-success-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span><strong>High Volume:</strong> Significant search demand detected</span>
            </li>
            @endif
            @if($opportunity->rank_potential >= 70)
            <li class="flex items-start">
                <svg class="w-5 h-5 text-success-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span><strong>Strong Ranking Potential:</strong> High likelihood of achieving top positions</span>
            </li>
            @endif
            @if($opportunity->competition_score < 50)
            <li class="flex items-start">
                <svg class="w-5 h-5 text-success-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span><strong>Low Competition:</strong> Less competitive market for faster rankings</span>
            </li>
            @endif
            @if($opportunity->estimated_monthly_revenue >= 200)
            <li class="flex items-start">
                <svg class="w-5 h-5 text-success-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span><strong>High Revenue Potential:</strong> Projected to generate significant monthly income</span>
            </li>
            @endif
        </ul>
    </div>

    @if($opportunity->identified_at)
    <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
        <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
            <span>Identified {{ $opportunity->identified_at->diffForHumans() }}</span>
            @if($opportunity->last_analyzed_at)
            <span>Updated {{ $opportunity->last_analyzed_at->diffForHumans() }}</span>
            @endif
        </div>
    </div>
    @endif
</div>
