<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $athlete->name }} - AthleteGum</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23111'/><text x='50' y='65' font-size='50' font-weight='bold' fill='white' text-anchor='middle'>A</text></svg>" />
    @php
        use Illuminate\Support\Str;
    @endphp
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-black" style="background-color: #000000;">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-black">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        @if($athlete->profile_photo)
                            <img src="{{ asset('storage/' . $athlete->profile_photo) }}" alt="{{ $athlete->name }}" class="h-16 w-16 rounded-full object-cover">
                        @else
                            <div class="h-16 w-16 rounded-full bg-gray-700 flex items-center justify-center">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h1 class="text-2xl font-bold text-white">{{ $athlete->name }}</h1>
                            @if($athlete->sport || $athlete->school)
                                <p class="text-sm text-gray-300">
                                    @if($athlete->sport){{ $athlete->sport }}@endif
                                    @if($athlete->sport && $athlete->school) • @endif
                                    @if($athlete->school){{ $athlete->school }}@endif
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="text-xs text-gray-400">
                        Powered by <span class="font-semibold text-white">AthleteGum</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-black">
            <!-- Social Proof Section -->
            @if(isset($completedDeals) && $completedDeals->count() > 0)
                <div class="bg-gray-900 rounded-lg shadow-sm p-6 mb-6">
                    <div class="mb-4">
                        <p class="text-sm font-medium text-white mb-1">Worked with {{ $completedDeals->count() }} {{ Str::plural('business', $completedDeals->count()) }}</p>
                    </div>

                    <!-- Business Logos/Names -->
                    @if(isset($businesses) && $businesses->count() > 0)
                        <div class="mb-6">
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4">
                                @foreach($businesses->take(6) as $business)
                                    <div class="flex items-center justify-center h-16 bg-gray-800 rounded-lg border border-gray-700">
                                        @if($business && isset($business->name) && $business->name)
                                            <span class="text-xs font-medium text-gray-200 text-center px-2">{{ Str::limit($business->name, 15) }}</span>
                                        @else
                                            <span class="text-xs text-gray-500">Business</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @if($businesses->count() > 6)
                                <p class="text-xs text-gray-400 mt-2 text-center">and {{ $businesses->count() - 6 }} more</p>
                            @endif
                        </div>
                    @endif

                    <!-- Deal Types Completed -->
                    @if(isset($dealTypes) && !empty($dealTypes) && is_array($dealTypes))
                        <div>
                            <p class="text-xs text-gray-400 mb-2">Deal types completed:</p>
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $dealTypeNames = \App\Models\Deal::getDealTypes();
                                @endphp
                                @foreach($dealTypes as $dealType)
                                    @if(isset($dealTypeNames[$dealType]))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-900 text-indigo-200">
                                            {{ $dealTypeNames[$dealType]['icon'] ?? '' }} {{ $dealTypeNames[$dealType]['name'] ?? $dealType }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-gray-900 rounded-lg shadow-sm p-6 mb-6 text-center">
                    <p class="text-sm text-gray-300">Ready to work with you</p>
                </div>
            @endif

            <!-- Primary CTA -->
            <div class="bg-gray-900 rounded-lg shadow-sm p-6 mb-6">
                @auth('web')
                    <a
                        href="{{ route('deals.create') }}?athlete={{ $athlete->id }}"
                        class="w-full inline-flex justify-center items-center px-6 py-3 bg-white border border-transparent rounded-md font-semibold text-base text-black hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition"
                    >
                        Work with me via AthleteGum
                    </a>
                @else
                    <div class="space-y-3">
                        <a
                            href="{{ route('login') }}?redirect={{ urlencode(route('deals.create') . '?athlete=' . $athlete->id) }}"
                            class="w-full inline-flex justify-center items-center px-6 py-3 bg-white border border-transparent rounded-md font-semibold text-base text-black hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition"
                        >
                            Work with me via AthleteGum
                        </a>
                        <p class="text-xs text-center text-gray-400">Sign in to create a deal, or continue as guest</p>
                    </div>
                @endauth
            </div>

            <!-- Social Media Links -->
            @if($athlete->instagram_handle || $athlete->tiktok_handle || $athlete->twitter_handle || $athlete->youtube_handle)
                <div class="bg-gray-900 rounded-lg shadow-sm p-6">
                    <h2 class="text-sm font-medium text-white mb-4">Connect</h2>
                    <div class="space-y-3">
                        @if($athlete->instagram_handle)
                            <a href="https://instagram.com/{{ $athlete->instagram_handle }}" target="_blank" rel="noopener" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 transition">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                                <span class="text-sm font-medium text-white">{{ '@' . $athlete->instagram_handle }}</span>
                            </a>
                        @endif

                        @if($athlete->tiktok_handle)
                            <a href="https://tiktok.com/@{{ $athlete->tiktok_handle }}" target="_blank" rel="noopener" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 transition">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
                                </svg>
                                <span class="text-sm font-medium text-white">{{ '@' . $athlete->tiktok_handle }}</span>
                            </a>
                        @endif

                        @if($athlete->twitter_handle)
                            <a href="https://x.com/{{ $athlete->twitter_handle }}" target="_blank" rel="noopener" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 transition">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                                <span class="text-sm font-medium text-white">{{ '@' . $athlete->twitter_handle }}</span>
                            </a>
                        @endif

                        @if($athlete->youtube_handle)
                            <a href="https://youtube.com/@{{ $athlete->youtube_handle }}" target="_blank" rel="noopener" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 transition">
                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                                <span class="text-sm font-medium text-white">{{ '@' . $athlete->youtube_handle }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="bg-black mt-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <p class="text-xs text-center text-gray-400">
                    Powered by <a href="{{ route('dashboard') }}" class="text-indigo-400 hover:text-indigo-300">AthleteGum</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

