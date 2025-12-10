<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Business') }}
            </h2>
            <a href="{{ route('businesses.index') }}" class="text-gray-600 hover:text-gray-800">
                ← Back to Businesses
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('businesses.store') }}" class="space-y-6">
                        @csrf

                        <!-- Business Name -->
                        <div>
                            <x-input-label for="name" :value="__('Business Name')" />
                            <x-text-input 
                                id="name" 
                                name="name" 
                                type="text" 
                                class="mt-1 block w-full" 
                                :value="old('name')" 
                                required 
                                autofocus 
                                placeholder="My Awesome Business"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            <p class="mt-1 text-sm text-gray-500">A unique name for your business. The slug will be auto-generated.</p>
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea 
                                id="description" 
                                name="description" 
                                rows="4"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="Brief description of your business..."
                            >{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Plan Selection -->
                        <div>
                            <x-input-label for="plan_id" :value="__('Select Plan')" />
                            <div class="mt-3 space-y-3">
                                @foreach($plans as $plan)
                                    <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors {{ $plan->slug === 'free' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                                        <input 
                                            type="radio" 
                                            name="plan_id" 
                                            value="{{ $plan->id }}" 
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 mt-0.5"
                                            {{ (old('plan_id', $plans->where('slug', 'free')->first()->id) == $plan->id) ? 'checked' : '' }}
                                        >
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-semibold text-gray-900">{{ $plan->name }}</span>
                                                <span class="text-2xl font-bold text-gray-900">
                                                    @if($plan->price == 0)
                                                        Free
                                                    @else
                                                        ${{ number_format($plan->price, 0) }}<span class="text-sm text-gray-500">/mo</span>
                                                    @endif
                                                </span>
                                            </div>
                                            
                                            @if($plan->description)
                                                <p class="mt-1 text-sm text-gray-600">{{ $plan->description }}</p>
                                            @endif

                                            <!-- Plan Features -->
                                            <div class="mt-3 space-y-2">
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span>
                                                        @if($plan->max_businesses === -1)
                                                            Unlimited businesses
                                                        @else
                                                            Up to {{ $plan->max_businesses }} {{ Str::plural('business', $plan->max_businesses) }}
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span>
                                                        @if($plan->max_users_per_business === -1)
                                                            Unlimited team members
                                                        @else
                                                            Up to {{ $plan->max_users_per_business }} {{ Str::plural('member', $plan->max_users_per_business) }} per business
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span>
                                                        @if($plan->max_projects === -1)
                                                            Unlimited projects
                                                        @else
                                                            Up to {{ $plan->max_projects }} {{ Str::plural('project', $plan->max_projects) }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>

                                            @if($plan->price > 0)
                                                <p class="mt-3 text-xs text-gray-500">
                                                    * Requires active Stripe subscription
                                                </p>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('plan_id')" />
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-3 pt-4">
                            <a href="{{ route('businesses.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <x-primary-button>
                                Create Business
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-800">What happens next?</h4>
                        <ul class="mt-2 text-sm text-blue-700 space-y-1">
                            <li>• You'll be automatically assigned as the <strong>Owner</strong> of this business</li>
                            <li>• The business will be created with your selected plan</li>
                            <li>• You can invite team members after creation</li>
                            <li>• You can upgrade or downgrade your plan anytime from the billing page</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
