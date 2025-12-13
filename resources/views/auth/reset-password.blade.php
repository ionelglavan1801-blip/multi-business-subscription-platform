<x-guest-layout>
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <div class="text-center mb-8">
            <div class="mx-auto w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Reset password</h2>
            <p class="mt-2 text-gray-600">Enter your new password below.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email address')" class="text-sm font-medium text-gray-700" />
                <x-text-input 
                    id="email" 
                    class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" 
                    type="email" 
                    name="email" 
                    :value="old('email', $request->email)" 
                    required 
                    autofocus 
                    autocomplete="username"
                    readonly
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('New password')" class="text-sm font-medium text-gray-700" />
                <x-text-input 
                    id="password" 
                    class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="new-password"
                    placeholder="••••••••" 
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm new password')" class="text-sm font-medium text-gray-700" />
                <x-text-input 
                    id="password_confirmation" 
                    class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    type="password"
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                    placeholder="••••••••" 
                />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    {{ __('Reset password') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
