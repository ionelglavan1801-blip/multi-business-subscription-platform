<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $project->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $business->name }}
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('businesses.projects.index', $business) }}" class="text-gray-600 hover:text-gray-900">
                    ← Back to Projects
                </a>
                @can('update', $project)
                    <a href="{{ route('businesses.projects.edit', [$business, $project]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Edit Project
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Project Details</h3>

                            <!-- Description -->
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Description</h4>
                                @if($project->description)
                                    <p class="text-gray-700 whitespace-pre-wrap">{{ $project->description }}</p>
                                @else
                                    <p class="text-gray-400 italic">No description provided.</p>
                                @endif
                            </div>

                            <!-- Project Content Placeholder -->
                            <div class="border-t pt-6">
                                <div class="bg-gray-50 rounded-lg p-8 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <h3 class="mt-4 text-sm font-medium text-gray-900">Project content coming soon</h3>
                                    <p class="mt-1 text-sm text-gray-500">Tasks, files, and more features will be added here.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $project->isActive() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </div>

                    <!-- Info Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Information</h3>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created by</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $project->creator?->name ?? 'Unknown' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $project->created_at->format('M d, Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $project->updated_at->format('M d, Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Danger Zone -->
                    @can('delete', $project)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-red-200">
                            <h3 class="text-lg font-medium text-red-600 mb-4">Danger Zone</h3>
                            <form method="POST" action="{{ route('businesses.projects.destroy', [$business, $project]) }}" onsubmit="return confirm('Are you sure you want to delete this project? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Delete Project
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
