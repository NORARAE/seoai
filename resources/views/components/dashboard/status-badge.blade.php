@props(['status'])

@php
    $statusConfig = [
        'published' => ['label' => 'Published', 'color' => 'green', 'icon' => '✓'],
        'draft' => ['label' => 'Draft', 'color' => 'gray', 'icon' => '○'],
        'archived' => ['label' => 'Archived', 'color' => 'orange', 'icon' => '📦'],
    ];
    
    $config = $statusConfig[$status] ?? ['label' => ucfirst($status), 'color' => 'gray', 'icon' => '•'];
    
    $colorClasses = [
        'green' => 'bg-green-100 text-green-800 border-green-200',
        'gray' => 'bg-gray-100 text-gray-700 border-gray-200',
        'orange' => 'bg-orange-100 text-orange-800 border-orange-200',
    ];
    
    $classes = $colorClasses[$config['color']] ?? $colorClasses['gray'];
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border {{ $classes }}">
    <span>{{ $config['icon'] }}</span>
    <span>{{ $config['label'] }}</span>
</span>
