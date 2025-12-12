@props(['title', 'value', 'icon' => null, 'href' => null, 'color' => 'indigo'])

@php
$colorClasses = [
    'indigo' => 'text-indigo-600',
    'green' => 'text-green-600',
    'blue' => 'text-blue-600',
    'purple' => 'text-purple-600',
    'yellow' => 'text-yellow-600',
    'red' => 'text-red-600',
    'gray' => 'text-gray-600',
];
$iconColor = $colorClasses[$color] ?? 'text-indigo-600';
@endphp

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200']) }}>
    @if($href)
        <a href="{{ $href }}" class="block p-6 hover:bg-gray-50 transition-colors duration-150">
    @else
        <div class="p-6">
    @endif
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 truncate">{{ $title }}</p>
                <p class="mt-1 text-3xl font-bold text-gray-900">{{ $value }}</p>
            </div>
            @if($icon)
                <div class="flex-shrink-0 {{ $iconColor }}">
                    {{ $icon }}
                </div>
            @endif
        </div>
        @if($slot->isNotEmpty())
            <div class="mt-4">
                {{ $slot }}
            </div>
        @endif
    @if($href)
        </a>
    @else
        </div>
    @endif
</div>
