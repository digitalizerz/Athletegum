<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AthleteGum - NIL Deal Execution Platform</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23111'/><text x='50' y='65' font-size='50' font-weight='bold' fill='white' text-anchor='middle'>A</text></svg>" />
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
<body class="font-sans antialiased bg-black" style="background-color: #000000;">
    <div class="min-h-screen flex flex-col bg-black" style="background-color: #000000;">
        <!-- Header -->
        <header class="bg-black border-b border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center">
                    <a href="{{ route('welcome') }}">
                        <x-athletegum-logo size="md" text-color="white" />
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-black" style="background-color: #000000;">
            <div class="w-full max-w-4xl">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Business Card -->
                    <div class="bg-white rounded-lg shadow-lg border-2 border-gray-200 hover:border-indigo-300 transition-colors p-8">
                        <div class="text-center mb-6">
                            <div class="mx-auto w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">I'm a Business</h2>
                            <p class="text-sm text-gray-600">Create and manage NIL deals with athletes</p>
                        </div>

                        <div class="space-y-3">
                            <a href="{{ route('register') }}" class="block w-full text-center px-6 py-3 bg-gray-900 text-white rounded-md font-semibold text-sm hover:bg-gray-800 transition">
                                Create Business Account
                            </a>
                            <a href="{{ route('login') }}" class="block w-full text-center px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-md font-semibold text-sm hover:border-gray-400 hover:bg-gray-50 transition">
                                Sign In to Business Account
                            </a>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-xs text-gray-500 text-center">For businesses, agencies, and collectives</p>
                        </div>
                    </div>

                    <!-- Athlete Card -->
                    <div class="bg-white rounded-lg shadow-lg border-2 border-gray-200 hover:border-indigo-300 transition-colors p-8">
                        <div class="text-center mb-6">
                            <div class="mx-auto w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">I'm an Athlete</h2>
                            <p class="text-sm text-gray-600">Create a shareable professional profile</p>
                        </div>

                        <div class="space-y-3">
                            <a href="{{ route('athlete.register') }}" class="block w-full text-center px-6 py-3 bg-gray-900 text-white rounded-md font-semibold text-sm hover:bg-gray-800 transition">
                                Create Athlete Profile
                            </a>
                            <a href="{{ route('athlete.login') }}" class="block w-full text-center px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-md font-semibold text-sm hover:border-gray-400 hover:bg-gray-50 transition">
                                Sign In to Athlete Profile
                            </a>
                </div>

                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-xs text-gray-500 text-center">Free profile • Shareable link • No marketplace</p>
                        </div>
                    </div>
                </div>
                </div>
            </main>
        </div>
    </body>
</html>
