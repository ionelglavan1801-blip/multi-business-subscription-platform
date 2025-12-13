<x-guest-layout>
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Welcome back</h2>
            <p class="mt-2 text-gray-600">Sign in to your account to continue</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
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
                    autocomplete="username"
                    placeholder="you@example.com" 
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between">
                    <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-700" />
                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-indigo-600 hover:text-indigo-500" href="{{ route('password.request') }}">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>
                <x-text-input 
                    id="password" 
                    class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password"
                    placeholder="••••••••" 
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                    name="remember"
                >
                <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                    {{ __('Remember me') }}
                </label>
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    {{ __('Sign in') }}
                </button>
            </div>
        </form>

        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Don't have an account?</span>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('register') }}" class="w-full flex justify-center py-3 px-4 border-2 border-gray-300 rounded-lg shadow-sm text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Create new account
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
