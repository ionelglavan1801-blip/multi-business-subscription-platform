<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MultiApp') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex">
            {{-- Left Side - Branding --}}
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 p-12 flex-col justify-between">
                <div>
                    <a href="/" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-white">MultiApp</span>
                    </a>
                </div>
                
                <div class="space-y-6">
                    <h1 class="text-4xl font-bold text-white leading-tight">
                        Manage all your businesses in one place
                    </h1>
                    <p class="text-indigo-100 text-lg">
                        Streamline subscriptions, team management, and billing with our powerful platform.
                    </p>
                    
                    <div class="flex items-center space-x-4 pt-4">
                        <div class="flex -space-x-2">
                            <div class="w-10 h-10 rounded-full bg-indigo-400 border-2 border-white flex items-center justify-center text-white font-semibold text-sm">JD</div>
                            <div class="w-10 h-10 rounded-full bg-purple-400 border-2 border-white flex items-center justify-center text-white font-semibold text-sm">MK</div>
                            <div class="w-10 h-10 rounded-full bg-pink-400 border-2 border-white flex items-center justify-center text-white font-semibold text-sm">AS</div>
                        </div>
                        <p class="text-indigo-100 text-sm">
                            <span class="font-semibold text-white">2,000+</span> businesses trust us
                        </p>
                    </div>
                </div>
                
                <div class="text-indigo-200 text-sm">
                    © {{ date('Y') }} MultiApp. All rights reserved.
                </div>
            </div>
            
            {{-- Right Side - Form --}}
            <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-8 bg-gray-50">
                {{-- Mobile Logo --}}
                <div class="lg:hidden mb-8">
                    <a href="/" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">MultiApp</span>
                    </a>
                </div>
                
                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
