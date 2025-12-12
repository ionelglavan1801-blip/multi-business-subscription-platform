@props(['plan'])

@php
$colors = [
    'free' => 'bg-gray-100 text-gray-800',
    'pro' => 'bg-blue-100 text-blue-800', 
    'enterprise' => 'bg-purple-100 text-purple-800',
];
$colorClass = $colors[$plan->slug ?? 'free'] ?? 'bg-gray-100 text-gray-800';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$colorClass}"]) }}>
    {{ $plan->name ?? 'Free' }}
</span>
