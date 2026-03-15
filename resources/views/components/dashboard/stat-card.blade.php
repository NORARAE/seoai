@props(['label', 'value', 'subtext' => null, 'color' => 'blue', 'icon' => null])

@php
    $colorClasses = [
        'blue' => 'bg-blue-50 border-blue-200 text-blue-700',
        'green' => 'bg-green-50 border-green-200 text-green-700',
        'yellow' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
        'orange' => 'bg-orange-50 border-orange-200 text-orange-700',
        'red' => 'bg-red-50 border-red-200 text-red-700',
        'gray' => 'bg-gray-50 border-gray-200 text-gray-700',
    ];
    
    $bgColor = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="bg-white rounded-lg border-2 border-gray-100 p-6 hover:shadow-lg transition-all duration-200 hover:border-{{ $color }}-200">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-600 mb-1">{{ $label }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $value }}</p>
            @if($subtext)
                <p class="text-xs text-gray-500 mt-2">{{ $subtext }}</p>
            @endif
        </div>
        @if($icon)
            <div class="ml-4 {{ $bgColor }} rounded-lg p-3 border">
                <span class="text-2xl">{{ $icon }}</span>
            </div>
        @endif
    </div>
</div>
