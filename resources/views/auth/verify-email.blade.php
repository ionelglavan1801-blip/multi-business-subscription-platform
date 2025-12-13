<x-guest-layout>
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <div class="text-center mb-8">
            <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Check your email</h2>
            <p class="mt-2 text-gray-600 max-w-sm mx-auto">
                We've sent a verification link to your email address. Please click the link to verify your account.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200">
                <div class="flex">
                    <svg class="w-5 h-5 text-green-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-green-700">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                </div>
            </div>
        @endif

        <div class="space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    {{ __('Resend verification email') }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex justify-center py-3 px-4 border-2 border-gray-300 rounded-lg shadow-sm text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    {{ __('Sign out') }}
                </button>
            </form>
        </div>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                Didn't receive the email? Check your spam folder or 
                <a href="#" class="text-indigo-600 hover:text-indigo-500">contact support</a>.
            </p>
        </div>
    </div>
</x-guest-layout>
