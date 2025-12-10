<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $business->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ $business->slug }}</p>
            </div>
            <div class="flex space-x-3">
                @can('update', $business)
                    <a href="{{ route('businesses.edit', $business) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Business
                    </a>
                @endcan
                <a href="{{ route('businesses.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    ← Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Business Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Business Overview</h3>
                    
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Business Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $business->name }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Slug</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $business->slug }}</dd>
                        </div>

                        @if($business->description)
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $business->description }}</dd>
                            </div>
                        @endif

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Plan</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $business->plan->slug === 'free' ? 'gray' : ($business->plan->slug === 'pro' ? 'blue' : 'purple') }}-100 text-{{ $business->plan->slug === 'free' ? 'gray' : ($business->plan->slug === 'pro' ? 'blue' : 'purple') }}-800">
                                    {{ $business->plan->name }} - ${{ number_format($business->plan->price, 2) }}/mo
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Your Role</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ ucfirst(auth()->user()->businesses()->where('business_id', $business->id)->first()?->pivot->role ?? 'member') }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $business->created_at->format('M d, Y') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $business->updated_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Team Members -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Team Members</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">
                                            {{ $business->users->count() }}
                                        </div>
                                        <div class="ml-2 text-sm text-gray-500">
                                            / {{ $business->plan->max_users_per_business === -1 ? '∞' : $business->plan->max_users_per_business }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Projects -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Projects</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">
                                            {{ $business->projects->count() }}
                                        </div>
                                        <div class="ml-2 text-sm text-gray-500">
                                            / {{ $business->plan->max_projects === -1 ? '∞' : $business->plan->max_projects }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subscription Status -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-{{ $business->subscription ? ($business->subscription->status === 'active' ? 'green' : 'yellow') : 'gray' }}-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Subscription</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-sm font-semibold text-gray-900">
                                            @if($business->subscription)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $business->subscription->status === 'active' ? 'green' : 'yellow' }}-100 text-{{ $business->subscription->status === 'active' ? 'green' : 'yellow' }}-800">
                                                    {{ ucfirst($business->subscription->status) }}
                                                </span>
                                            @else
                                                <span class="text-gray-500">No active subscription</span>
                                            @endif
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Members List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Team Members</h3>
                        <a href="{{ route('businesses.team', $business) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            Manage Team →
                        </a>
                    </div>

                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">User</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Joined</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($business->users as $user)
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                            {{ $user->name }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                {{ ucfirst($user->pivot->role) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $user->pivot->created_at ? $user->pivot->created_at->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            @if($user->pivot->role !== 'owner')
                                                <button class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Projects -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Projects</h3>
                        <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            View All Projects →
                        </button>
                    </div>

                    @if($business->projects->count() > 0)
                        <div class="space-y-3">
                            @foreach($business->projects->take(5) as $project)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-indigo-300 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="h-8 w-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                        </svg>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ $project->name }}</h4>
                                            <p class="text-xs text-gray-500">Created {{ $project->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No projects</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new project.</p>
                            <div class="mt-6">
                                <button class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Create Project
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Danger Zone -->
            @can('delete', $business)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-red-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-red-600 mb-2">Danger Zone</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Once you delete a business, there is no going back. All data including projects, invitations, and subscriptions will be permanently deleted.
                        </p>
                        <form method="POST" action="{{ route('businesses.destroy', $business) }}" onsubmit="return confirm('Are you absolutely sure? This action cannot be undone. Type DELETE to confirm.');">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>
                                Delete This Business
                            </x-danger-button>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
