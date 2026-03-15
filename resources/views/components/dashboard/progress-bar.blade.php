@props(['label', 'percentage', 'color' => 'blue'])

@php
    $colorClasses = [
        'blue' => 'bg-blue-500',
        'green' => 'bg-green-500',
        'yellow' => 'bg-yellow-500',
        'orange' => 'bg-orange-500',
        'red' => 'bg-red-500',
        'gray' => 'bg-gray-500',
    ];
    
    $barColor = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="mb-4">
    <div class="flex justify-between items-center mb-2">
        <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
        <span class="text-sm font-bold text-gray-900">{{ number_format($percentage, 1) }}%</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-2.5">
        <div class="{{ $barColor }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
    </div>
</div>
