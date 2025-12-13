<x-guest-layout>
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <div class="text-center mb-8">
            <div class="mx-auto w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Forgot password?</h2>
            <p class="mt-2 text-gray-600">No worries, we'll send you reset instructions.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email address')" class="text-sm font-medium text-gray-700" />
                <x-text-input 
                    id="email" 
                    class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus
                    placeholder="you@example.com" 
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    {{ __('Send reset link') }}
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to sign in
            </a>
        </div>
    </div>
</x-guest-layout>
