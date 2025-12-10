<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Accept Invitation
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <!-- Invitation Icon -->
                    <div class="flex justify-center mb-6">
                        <div class="flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Invitation Details -->
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">
                            You've been invited!
                        </h3>
                        <p class="text-gray-600">
                            <span class="font-medium">{{ $invitation['inviter_name'] }}</span> has invited you to join
                            <span class="font-medium text-indigo-600">{{ $invitation['business_name'] }}</span>
                            as a <span class="font-medium">{{ ucfirst($invitation['role']) }}</span>.
                        </p>
                    </div>

                    <!-- Invitation Info -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <dl class="space-y-4">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Business</dt>
                                <dd class="text-sm text-gray-900 font-semibold">{{ $invitation['business_name'] }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Your Role</dt>
                                <dd class="text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ ucfirst($invitation['role']) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Invited by</dt>
                                <dd class="text-sm text-gray-900">{{ $invitation['inviter_name'] }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Expires</dt>
                                <dd class="text-sm text-red-600 font-medium">{{ $invitation['expires_at'] }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- What happens next -->
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-blue-800">What happens when you accept?</h4>
                                <ul class="mt-2 text-sm text-blue-700 list-disc list-inside space-y-1">
                                    <li>You'll gain access to {{ $invitation['business_name'] }}</li>
                                    <li>You'll be able to collaborate with the team</li>
                                    <li>You can manage projects and resources based on your role</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <form method="POST" action="{{ route('invitations.accept', $invitation['token']) }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Accept Invitation
                            </button>
                        </form>

                        <a href="{{ route('dashboard') }}" class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Maybe Later
                        </a>
                    </div>

                    <!-- Fine Print -->
                    <p class="mt-6 text-center text-xs text-gray-500">
                        By accepting this invitation, you agree to collaborate with this business according to your assigned role.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
