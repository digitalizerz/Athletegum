<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>About - AthleteGum</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23111'/><text x='50' y='65' font-size='50' font-weight='bold' fill='white' text-anchor='middle'>A</text></svg>" />
        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white text-black">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 h-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between">
                <a href="{{ route('welcome') }}">
                    <x-athletegum-logo size="md" text-color="default" />
                </a>
                <div class="relative" x-data="{ open: false }">
                    <button 
                        @click="open = !open"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:border-gray-400 transition text-sm font-medium"
                    >
                        Log in
                    </button>
                    <div 
                        x-show="open"
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                        style="display: none;"
                    >
                        <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-gray-900 hover:bg-gray-100">
                            Log in as Business
                        </a>
                        <a href="{{ route('athlete.login') }}" class="block px-4 py-2 text-sm text-gray-900 hover:bg-gray-100">
                            Log in as Athlete
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="py-16 lg:py-20">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-4xl md:text-5xl font-bold mb-8">About AthleteGum</h1>
                
                <div class="prose prose-lg max-w-none">
                    <p class="text-lg text-gray-700 mb-8 leading-relaxed">
                        AthleteGum helps businesses pay athletes for real work — safely and transparently.
                    </p>
                    
                    <p class="text-gray-700 mb-8 leading-relaxed">
                        AthleteGum is a simple platform designed to make NIL deals clear, fair, and easy for both sides. Businesses create deals, define deliverables, and fund them upfront. Athletes complete the work and get paid only after the work is approved.
                    </p>
                    
                    <p class="text-gray-700 mb-12 leading-relaxed">
                        We remove confusion, protect both parties, and focus on real work — not hype or marketplaces.
                    </p>

                    <h2 class="text-2xl font-bold mb-6 mt-12">Why AthleteGum Exists</h2>
                    <p class="text-gray-700 mb-6 leading-relaxed">
                        Paying athletes shouldn't be risky or complicated.
                    </p>
                    <p class="text-gray-700 mb-4 leading-relaxed">
                        Many businesses hesitate to work with athletes because of:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-8 space-y-2 ml-4">
                        <li>Unclear expectations</li>
                        <li>Unverified deliverables</li>
                        <li>Payment disputes</li>
                        <li>Trust issues on both sides</li>
                    </ul>
                    <p class="text-gray-700 mb-12 leading-relaxed">
                        AthleteGum solves this by holding funds securely until work is completed and approved.
                    </p>

                    <h2 class="text-2xl font-bold mb-6 mt-12">How AthleteGum Works</h2>
                    <ul class="list-disc list-inside text-gray-700 mb-8 space-y-2 ml-4">
                        <li>Businesses create deals and define deliverables</li>
                        <li>Funds are held securely until work is done</li>
                        <li>Athletes submit deliverables directly in the platform</li>
                        <li>Payments are released only after approval</li>
                    </ul>
                    <p class="text-gray-700 mb-12 leading-relaxed">
                        No contracts to chase. No awkward payment conversations.
                    </p>

                    <h2 class="text-2xl font-bold mb-6 mt-12">Who AthleteGum Is For</h2>
                    
                    <h3 class="text-xl font-semibold mb-4 mt-8">For Businesses</h3>
                    <ul class="list-disc list-inside text-gray-700 mb-8 space-y-2 ml-4">
                        <li>Small businesses</li>
                        <li>Local brands</li>
                        <li>Agencies and collectives</li>
                        <li>One-time or recurring NIL campaigns</li>
                    </ul>

                    <h3 class="text-xl font-semibold mb-4 mt-8">For Athletes</h3>
                    <ul class="list-disc list-inside text-gray-700 mb-8 space-y-2 ml-4">
                        <li>College athletes</li>
                        <li>Creators and influencers</li>
                        <li>Individuals or teams doing real promotional work</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-6 mt-12">Our Focus</h2>
                    <ul class="list-disc list-inside text-gray-700 mb-8 space-y-2 ml-4">
                        <li>Trust</li>
                        <li>Transparency</li>
                        <li>Clear expectations</li>
                        <li>Fair payments</li>
                    </ul>
                    
                    <p class="text-gray-700 mb-8 leading-relaxed font-medium">
                        AthleteGum is not a marketplace.<br />
                        It's a deal execution platform.
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <x-athletegum-logo size="sm" text-color="default" />
                    
                    <div class="flex flex-wrap justify-center gap-4 text-sm text-gray-600">
                        <a href="{{ route('pages.about') }}" class="hover:text-black transition">About</a>
                        <span class="text-gray-300">·</span>
                        <a href="{{ route('pages.terms') }}" class="hover:text-black transition">Terms</a>
                        <span class="text-gray-300">·</span>
                        <a href="{{ route('pages.privacy') }}" class="hover:text-black transition">Privacy</a>
                        <span class="text-gray-300">·</span>
                        <a href="{{ route('pages.contact') }}" class="hover:text-black transition">Contact</a>
                    </div>
                </div>
                
                <div class="mt-8 pt-8 border-t border-gray-200 text-center text-xs text-gray-500">
                    <p>&copy; {{ date('Y') }} AthleteGum. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </body>
</html>

