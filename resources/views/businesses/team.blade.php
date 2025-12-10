<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Team Management - {{ $business->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Manage team members and invitations</p>
            </div>
            <a href="{{ route('businesses.show', $business) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                ← Back to Business
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Invite New Member Form -->
            @can('create', [App\Models\Invitation::class, $business])
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Invite New Member</h3>
                        
                        <form method="POST" action="{{ route('businesses.invitations.store', $business) }}" class="space-y-4">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                           placeholder="colleague@example.com">
                                </div>

                                <!-- Role -->
                                <div>
                                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                                    <select name="role" id="role" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="member" {{ old('role') === 'member' ? 'selected' : '' }}>Member</option>
                                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-2">
                                <p class="text-sm text-gray-500">
                                    An invitation email will be sent to this address.
                                </p>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Send Invitation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan

            <!-- Pending Invitations -->
            @if($pendingInvitations->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pending Invitations</h3>
                        
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Email</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Invited By</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Expires</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($pendingInvitations as $invitation)
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                                {{ $invitation->email }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    {{ ucfirst($invitation->role) }}
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ $invitation->inviter->name }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ $invitation->expires_at->diffForHumans() }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                                @if($invitation->isExpired())
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Expired
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium space-x-2">
                                                @can('resend', $invitation)
                                                    <form method="POST" action="{{ route('businesses.invitations.resend', [$business, $invitation]) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-indigo-600 hover:text-indigo-900">
                                                            Resend
                                                        </button>
                                                    </form>
                                                @endcan

                                                @can('cancel', $invitation)
                                                    <form method="POST" action="{{ route('businesses.invitations.destroy', [$business, $invitation]) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" onclick="return confirm('Are you sure you want to cancel this invitation?')" class="text-red-600 hover:text-red-900">
                                                            Cancel
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Current Team Members -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Team Members ({{ $business->users->count() }})</h3>
                    
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">User</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Joined</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($business->users as $user)
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                            {{ $user->name }}
                                            @if($user->id === auth()->id())
                                                <span class="text-gray-500">(You)</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $user->email }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $user->pivot->role === 'owner' ? 'purple' : ($user->pivot->role === 'admin' ? 'blue' : 'gray') }}-100 text-{{ $user->pivot->role === 'owner' ? 'purple' : ($user->pivot->role === 'admin' ? 'blue' : 'gray') }}-800">
                                                {{ ucfirst($user->pivot->role) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $user->pivot->created_at?->format('M d, Y') ?? 'N/A' }}
                                        </td>
                                        <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium">
                                            @if($user->pivot->role !== 'owner' && $user->id !== auth()->id())
                                                @can('update', $business)
                                                    <button class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                        Edit Role
                                                    </button>
                                                    <button onclick="return confirm('Are you sure you want to remove this user?')" class="text-red-600 hover:text-red-900">
                                                        Remove
                                                    </button>
                                                @endcan
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
