<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Billing & Subscription') }} - {{ $business->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                    {{ session('info') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Current Plan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Current Plan</h3>
                    
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h4 class="text-2xl font-bold">{{ $business->plan->name }}</h4>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full
                                    @if($business->plan->slug === 'free') bg-gray-200 text-gray-800
                                    @elseif($business->plan->slug === 'pro') bg-blue-200 text-blue-800
                                    @else bg-purple-200 text-purple-800
                                    @endif">
                                    ${{ number_format($business->plan->price_monthly / 100, 2) }}/month
                                </span>
                            </div>
                            
                            <p class="text-gray-600 mb-4">{{ $business->plan->description }}</p>
                            
                            <div class="space-y-1 text-sm">
                                <p>✓ {{ $business->plan->max_businesses ?? 'Unlimited' }} businesses</p>
                                <p>✓ {{ $business->plan->max_users_per_business ?? 'Unlimited' }} team members per business</p>
                                <p>✓ {{ $business->plan->max_projects ?? 'Unlimited' }} projects</p>
                            </div>
                        </div>

                        @if($business->activeSubscription)
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                    @if($business->activeSubscription->status === 'active') bg-green-100 text-green-800
                                    @elseif($business->activeSubscription->status === 'past_due') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($business->activeSubscription->status) }}
                                </span>
                                
                                @if($business->activeSubscription->ends_at)
                                    <p class="text-sm text-gray-600 mt-2">
                                        Cancels on {{ $business->activeSubscription->ends_at->format('M d, Y') }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-600 mt-2">
                                        Renews on {{ $business->activeSubscription->current_period_end->format('M d, Y') }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if($business->activeSubscription)
                        <div class="mt-6 flex gap-3">
                            <a href="{{ route('billing.portal', $business) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Manage Payment Methods
                            </a>

                            @if($business->activeSubscription->ends_at)
                                <form action="{{ route('billing.resume-subscription', $business) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                                        Resume Subscription
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('billing.cancel-subscription', $business) }}" method="POST"
                                      onsubmit="return confirm('Are you sure you want to cancel your subscription? It will remain active until the end of the billing period.')">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
                                        Cancel Subscription
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Available Plans --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-6">Available Plans</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($plans as $plan)
                            <div class="border rounded-lg p-6 {{ $business->plan_id === $plan->id ? 'ring-2 ring-blue-500' : '' }}">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-xl font-bold">{{ $plan->name }}</h4>
                                    @if($business->plan_id === $plan->id)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded">
                                            Current
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-4">
                                    <span class="text-3xl font-bold">${{ number_format($plan->price_monthly / 100, 2) }}</span>
                                    <span class="text-gray-600">/month</span>
                                </div>

                                <p class="text-sm text-gray-600 mb-4">{{ $plan->description }}</p>

                                <ul class="space-y-2 mb-6 text-sm">
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ $plan->max_businesses ?? 'Unlimited' }} businesses
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ $plan->max_users_per_business ?? 'Unlimited' }} team members
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ $plan->max_projects ?? 'Unlimited' }} projects
                                    </li>
                                </ul>

                                @if($business->plan_id === $plan->id)
                                    <button disabled 
                                            class="w-full px-4 py-2 bg-gray-300 text-gray-500 rounded-md font-semibold text-sm cursor-not-allowed">
                                        Current Plan
                                    </button>
                                @elseif($plan->price_monthly === 0)
                                    <p class="text-sm text-gray-500 text-center">Downgrade available through support</p>
                                @else
                                    <form action="{{ route('billing.checkout', $business) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                        <button type="submit" 
                                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-md font-semibold text-sm hover:bg-blue-700">
                                            {{ $business->plan->price_monthly < $plan->price_monthly ? 'Upgrade' : 'Change' }} to {{ $plan->name }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
