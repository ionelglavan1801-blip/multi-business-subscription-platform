@props(['label', 'value', 'max', 'showLabel' => true, 'size' => 'md'])

@php
$percentage = $max > 0 ? min(100, ($value / $max) * 100) : 0;

// Color based on usage
if ($percentage >= 90) {
    $barColor = 'bg-red-500';
    $textColor = 'text-red-600';
} elseif ($percentage >= 70) {
    $barColor = 'bg-yellow-500';
    $textColor = 'text-yellow-600';
} else {
    $barColor = 'bg-green-500';
    $textColor = 'text-green-600';
}

$heightClass = match($size) {
    'sm' => 'h-1.5',
    'lg' => 'h-3',
    default => 'h-2',
};
@endphp

<div {{ $attributes }}>
    @if($showLabel)
        <div class="flex justify-between text-sm mb-1">
            <span class="font-medium text-gray-700">{{ $label }}</span>
            <span class="{{ $textColor }} font-medium">{{ $value }}/{{ $max }}</span>
        </div>
    @endif
    <div class="w-full bg-gray-200 rounded-full {{ $heightClass }}">
        <div class="{{ $barColor }} {{ $heightClass }} rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
    </div>
</div>
