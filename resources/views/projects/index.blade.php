<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Projects') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $business->name }}
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('businesses.show', $business) }}" class="text-gray-600 hover:text-gray-900">
                    ← Back to Business
                </a>
                @can('create', [App\Models\Project::class, $business])
                    <a href="{{ route('businesses.projects.create', $business) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        New Project
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <x-alert type="success" :message="session('success')" class="mb-4" />
            @endif

            <!-- Usage Bar -->
            @php
                $maxProjects = $business->plan?->max_projects;
                $currentProjects = $business->projects()->count();
            @endphp
            @if($maxProjects)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <x-usage-bar label="Project Usage" :value="$currentProjects" :max="$maxProjects" />
                    @if($business->remainingProjectSlots() === 0)
                        <p class="text-sm text-amber-600 mt-2">
                            You've reached your project limit. <a href="{{ route('billing.index', $business) }}" class="underline">Upgrade your plan</a> to create more.
                        </p>
                    @endif
                </div>
            @endif

            <!-- Projects Grid -->
            @if($projects->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($projects as $project)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200">
                            <div class="p-6">
                                <!-- Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('businesses.projects.show', [$business, $project]) }}" class="hover:text-indigo-600">
                                            <h3 class="text-lg font-semibold text-gray-900 truncate">
                                                {{ $project->name }}
                                            </h3>
                                        </a>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $project->isActive() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </div>

                                <!-- Description -->
                                @if($project->description)
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                        {{ $project->description }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-400 italic mb-4">
                                        No description
                                    </p>
                                @endif

                                <!-- Meta -->
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <span>Created by {{ $project->creator?->name ?? 'Unknown' }}</span>
                                    <span>{{ $project->created_at->diffForHumans() }}</span>
                                </div>

                                <!-- Actions -->
                                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end space-x-2">
                                    <a href="{{ route('businesses.projects.show', [$business, $project]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        View
                                    </a>
                                    @can('update', $project)
                                        <a href="{{ route('businesses.projects.edit', [$business, $project]) }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                                            Edit
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $projects->links() }}
                </div>
            @else
                <x-empty-state
                    title="No projects yet"
                    description="Create your first project to get started."
                    icon='<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>'
                >
                    @can('create', [App\Models\Project::class, $business])
                        <a href="{{ route('businesses.projects.create', $business) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Create First Project
                        </a>
                    @endcan
                </x-empty-state>
            @endif
        </div>
    </div>
</x-app-layout>
