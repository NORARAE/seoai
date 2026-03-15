@props(['label', 'count', 'color' => 'gray', 'urgent' => false])

@php
    $colorClasses = [
        'blue' => 'bg-blue-100 text-blue-800 border-blue-200',
        'green' => 'bg-green-100 text-green-800 border-green-200',
        'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'orange' => 'bg-orange-100 text-orange-800 border-orange-200',
        'red' => 'bg-red-100 text-red-800 border-red-200',
        'gray' => 'bg-gray-100 text-gray-800 border-gray-200',
    ];
    
    $classes = $colorClasses[$color] ?? $colorClasses['gray'];
@endphp

<div class="flex items-center justify-between py-3 px-4 rounded-lg border {{ $classes }} {{ $urgent ? 'ring-2 ring-offset-1 ring-' . $color . '-400' : '' }}">
    <div class="flex items-center gap-3">
        @if($urgent)
            <span class="text-lg">⚠️</span>
        @endif
        <span class="font-medium text-sm">{{ $label }}</span>
    </div>
    <div class="flex items-center gap-2">
        <span class="font-bold text-lg">{{ $count }}</span>
        @if($count > 0)
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        @endif
    </div>
</div>
