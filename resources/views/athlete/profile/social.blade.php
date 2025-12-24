<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Social Media - AthleteGum</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23111'/><text x='50' y='65' font-size='50' font-weight='bold' fill='white' text-anchor='middle'>A</text></svg>" />
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50">
        <div class="mb-6">
            <a href="{{ route('welcome') }}">
                <x-athletegum-logo size="lg" text-color="default" />
            </a>
        </div>
        <div class="w-full sm:max-w-2xl mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Add your social media (optional)</h1>
                <p class="text-sm text-gray-600">These links help businesses learn more about you. No follower counts or metrics will be displayed.</p>
            </div>

            <form method="POST" action="{{ route('athlete.profile.social.store') }}">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label for="instagram_handle" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                                <span>Instagram</span>
                            </div>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                <span class="text-gray-400 font-medium">@</span>
                            </div>
                            <x-text-input
                                id="instagram_handle"
                                type="text"
                                name="instagram_handle"
                                value="{{ old('instagram_handle', ltrim($athlete->instagram_handle ?? '', '@')) }}"
                                class="block w-full pl-8 pr-3"
                                placeholder="username"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('instagram_handle')" class="mt-1" />
                    </div>

                    <div>
                        <label for="tiktok_handle" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
                                </svg>
                                <span>TikTok</span>
                            </div>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                <span class="text-gray-400 font-medium">@</span>
                            </div>
                            <x-text-input
                                id="tiktok_handle"
                                type="text"
                                name="tiktok_handle"
                                value="{{ old('tiktok_handle', ltrim($athlete->tiktok_handle ?? '', '@')) }}"
                                class="block w-full pl-8 pr-3"
                                placeholder="username"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('tiktok_handle')" class="mt-1" />
                    </div>

                    <div>
                        <label for="twitter_handle" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                                <span>X (Twitter)</span>
                            </div>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                <span class="text-gray-400 font-medium">@</span>
                            </div>
                            <x-text-input
                                id="twitter_handle"
                                type="text"
                                name="twitter_handle"
                                value="{{ old('twitter_handle', ltrim($athlete->twitter_handle ?? '', '@')) }}"
                                class="block w-full pl-8 pr-3"
                                placeholder="username"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('twitter_handle')" class="mt-1" />
                    </div>

                    <div>
                        <label for="youtube_handle" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                                <span>YouTube</span>
                            </div>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                <span class="text-gray-400 font-medium">@</span>
                            </div>
                            <x-text-input
                                id="youtube_handle"
                                type="text"
                                name="youtube_handle"
                                value="{{ old('youtube_handle', ltrim($athlete->youtube_handle ?? '', '@')) }}"
                                class="block w-full pl-8 pr-3"
                                placeholder="channel"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('youtube_handle')" class="mt-1" />
                    </div>
                </div>

                <div class="mt-8 flex justify-between">
                    <a href="{{ route('athlete.profile.setup') }}" class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:text-gray-900">
                        ← Back
                    </a>
                    <div class="flex space-x-3">
                        <a href="{{ route('athlete.profile.preview') }}" class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:text-gray-900">
                            Skip for now
                        </a>
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-gray-900 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                            Continue
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

