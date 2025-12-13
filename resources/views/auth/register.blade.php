<x-guest-layout>
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Create your account</h2>
            <p class="mt-2 text-gray-600">Start managing your businesses today</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Full name')" class="text-sm font-medium text-gray-700" />
                <x-text-input 
                    id="name" 
                    class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" 
                    type="text" 
                    name="name" 
                    :value="old('name')" 
                    required 
                    autofocus 
                    autocomplete="name"
                    placeholder="John Doe" 
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

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
                    autocomplete="username"
                    placeholder="you@example.com" 
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-700" />
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
                <x-input-label for="password_confirmation" :value="__('Confirm password')" class="text-sm font-medium text-gray-700" />
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

            <!-- Terms -->
            <div class="flex items-start">
                <input 
                    id="terms" 
                    type="checkbox" 
                    class="h-4 w-4 mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                    required
                >
                <label for="terms" class="ml-2 block text-sm text-gray-600">
                    I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-500">Terms of Service</a> and <a href="#" class="text-indigo-600 hover:text-indigo-500">Privacy Policy</a>
                </label>
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    {{ __('Create account') }}
                </button>
            </div>
        </form>

        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Already have an account?</span>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('login') }}" class="w-full flex justify-center py-3 px-4 border-2 border-gray-300 rounded-lg shadow-sm text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Sign in instead
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
