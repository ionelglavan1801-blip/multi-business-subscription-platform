@props(['businesses', 'currentBusiness'])

<x-dropdown align="left" width="64">
    <x-slot name="trigger">
        <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
            @if($currentBusiness)
                <svg class="mr-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="truncate max-w-xs">{{ $currentBusiness->name }}</span>
                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $currentBusiness->plan->slug === 'free' ? 'gray' : ($currentBusiness->plan->slug === 'pro' ? 'blue' : 'purple') }}-100 text-{{ $currentBusiness->plan->slug === 'free' ? 'gray' : ($currentBusiness->plan->slug === 'pro' ? 'blue' : 'purple') }}-800">
                    {{ $currentBusiness->plan->name }}
                </span>
            @else
                <svg class="mr-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Create Business</span>
            @endif

            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </x-slot>

    <x-slot name="content">
        <div class="px-4 py-3 border-b border-gray-100">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Your Businesses</p>
        </div>

        @forelse($businesses as $business)
            <form method="POST" action="{{ route('businesses.switch', $business) }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 flex items-center justify-between transition duration-150 ease-in-out {{ $currentBusiness && $currentBusiness->id === $business->id ? 'bg-gray-50' : '' }}">
                    <div class="flex items-center flex-1 min-w-0">
                        <svg class="mr-3 h-5 w-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium truncate">{{ $business->name }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst($business->pivot->role) }}</p>
                        </div>
                    </div>
                    @if($currentBusiness && $currentBusiness->id === $business->id)
                        <svg class="h-5 w-5 text-indigo-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </button>
            </form>
        @empty
            <div class="px-4 py-3 text-sm text-gray-500">
                No businesses yet
            </div>
        @endforelse

        <div class="border-t border-gray-100">
            <x-dropdown-link :href="route('businesses.index')">
                <svg class="mr-2 h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                View All Businesses
            </x-dropdown-link>
            
            <x-dropdown-link :href="route('businesses.create')">
                <svg class="mr-2 h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create New Business
            </x-dropdown-link>
        </div>
    </x-slot>
</x-dropdown>
