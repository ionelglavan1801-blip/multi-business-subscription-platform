<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            @if($currentBusiness)
                <x-plan-badge :plan="$plan" />
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Flash Messages --}}
            @if(session('success'))
                <x-alert type="success" class="mb-6" dismissible>{{ session('success') }}</x-alert>
            @endif
            @if(session('error'))
                <x-alert type="error" class="mb-6" dismissible>{{ session('error') }}</x-alert>
            @endif

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <x-stat-card 
                    title="My Businesses" 
                    :value="$businessCount"
                    color="indigo"
                    href="{{ route('businesses.index') }}"
                >
                    <x-slot name="icon">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card 
                    title="Current Plan" 
                    :value="$plan->name ?? 'No Plan'"
                    color="blue"
                    href="{{ $currentBusiness ? route('billing.index', $currentBusiness) : '#' }}"
                >
                    <x-slot name="icon">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card 
                    title="Team Members" 
                    :value="$teamMemberCount"
                    color="green"
                    href="{{ $currentBusiness ? route('businesses.team', $currentBusiness) : '#' }}"
                >
                    <x-slot name="icon">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card 
                    title="Projects" 
                    :value="$projectCount"
                    color="purple"
                >
                    <x-slot name="icon">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                    </x-slot>
                </x-stat-card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Quick Actions --}}
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <a href="{{ route('businesses.create') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200">
                                    <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-2">
                                        <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-900">Create New Business</span>
                                </a>
                                
                                @if($currentBusiness)
                                    <a href="{{ route('businesses.team', $currentBusiness) }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200">
                                        <div class="flex-shrink-0 bg-green-100 rounded-lg p-2">
                                            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                            </svg>
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-900">Invite Team Member</span>
                                    </a>
                                    
                                    <a href="{{ route('billing.index', $currentBusiness) }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200">
                                        <div class="flex-shrink-0 bg-blue-100 rounded-lg p-2">
                                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-900">Manage Billing</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- My Businesses --}}
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">My Businesses</h3>
                                <a href="{{ route('businesses.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All →</a>
                            </div>
                            
                            @if($businesses->isEmpty())
                                <x-empty-state 
                                    title="No businesses yet"
                                    description="Create your first business to get started."
                                    action="{{ route('businesses.create') }}"
                                    actionText="Create Business"
                                />
                            @else
                                <div class="space-y-3">
                                    @foreach($businesses as $business)
                                        <a href="{{ route('businesses.show', $business) }}" class="flex items-center justify-between p-4 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200 {{ $currentBusiness && $currentBusiness->id === $business->id ? 'ring-2 ring-indigo-500 bg-indigo-50' : '' }}">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                                    {{ strtoupper(substr($business->name, 0, 2)) }}
                                                </div>
                                                <div class="ml-4">
                                                    <p class="text-sm font-medium text-gray-900">{{ $business->name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $business->users()->count() }} members</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <x-plan-badge :plan="$business->plan" />
                                                @if($currentBusiness && $currentBusiness->id === $business->id)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">Current</span>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Plan Usage (if current business exists) --}}
            @if($currentBusiness && $plan)
                <div class="mt-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Plan Usage - {{ $currentBusiness->name }}</h3>
                                <a href="{{ route('billing.index', $currentBusiness) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Upgrade Plan →</a>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <x-usage-bar 
                                    label="Team Members" 
                                    :value="$teamMemberCount" 
                                    :max="$plan->max_users_per_business ?? 999"
                                />
                                <x-usage-bar 
                                    label="Projects" 
                                    :value="$projectCount" 
                                    :max="$plan->max_projects ?? 999"
                                />
                                <x-usage-bar 
                                    label="Businesses" 
                                    :value="$businessCount" 
                                    :max="$plan->max_businesses ?? 999"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
