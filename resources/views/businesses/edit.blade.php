<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Business') }}
            </h2>
            <a href="{{ route('businesses.show', $business) }}" class="text-gray-600 hover:text-gray-800">
                ← Back to Business
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('businesses.update', $business) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- Business Name -->
                        <div>
                            <x-input-label for="name" :value="__('Business Name')" />
                            <x-text-input 
                                id="name" 
                                name="name" 
                                type="text" 
                                class="mt-1 block w-full" 
                                :value="old('name', $business->name)" 
                                required 
                                autofocus 
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            <p class="mt-1 text-sm text-gray-500">
                                Current slug: <span class="font-mono text-indigo-600">{{ $business->slug }}</span>
                            </p>
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
                            >{{ old('description', $business->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Current Plan Info (Read-only) -->
                        <div>
                            <x-input-label :value="__('Current Plan')" />
                            <div class="mt-2 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $business->plan->name }}</p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            @if($business->plan->price == 0)
                                                Free plan
                                            @else
                                                ${{ number_format($business->plan->price, 2) }}/month
                                            @endif
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $business->plan->slug === 'free' ? 'gray' : ($business->plan->slug === 'pro' ? 'blue' : 'purple') }}-100 text-{{ $business->plan->slug === 'free' ? 'gray' : ($business->plan->slug === 'pro' ? 'blue' : 'purple') }}-800">
                                        {{ $business->plan->name }}
                                    </span>
                                </div>
                                <div class="mt-3 space-y-1 text-sm text-gray-600">
                                    <p>• {{ $business->plan->max_businesses === -1 ? 'Unlimited' : $business->plan->max_businesses }} businesses</p>
                                    <p>• {{ $business->plan->max_users_per_business === -1 ? 'Unlimited' : $business->plan->max_users_per_business }} team members per business</p>
                                    <p>• {{ $business->plan->max_projects === -1 ? 'Unlimited' : $business->plan->max_projects }} projects</p>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                To change your plan, visit the 
                                <a href="#" class="text-indigo-600 hover:text-indigo-800 font-medium">Billing page</a>
                            </p>
                        </div>

                        <!-- Business Stats (Read-only) -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label :value="__('Created')" />
                                <p class="mt-1 text-sm text-gray-900">{{ $business->created_at->format('F d, Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $business->created_at->diffForHumans() }}</p>
                            </div>
                            <div>
                                <x-input-label :value="__('Last Updated')" />
                                <p class="mt-1 text-sm text-gray-900">{{ $business->updated_at->format('F d, Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $business->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <a href="{{ route('businesses.show', $business) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <x-primary-button>
                                Update Business
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-800">Important Notes</h4>
                        <ul class="mt-2 text-sm text-blue-700 space-y-1">
                            <li>• Changing the business name will not affect the slug (URL)</li>
                            <li>• Team members will be notified of any changes</li>
                            <li>• Plan changes must be done through the Billing page</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
