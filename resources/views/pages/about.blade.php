<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="color-scheme" content="light">
        <title>About - AthleteGum</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23111'/><text x='50' y='65' font-size='50' font-weight='bold' fill='white' text-anchor='middle'>A</text></svg>" />
        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white text-black" style="color-scheme: light !important;">
        <!-- Header -->
        <header x-data="{ open: false }" class="bg-black border-b border-white/10 h-16 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between">
                <!-- Logo -->
                <a href="{{ route('welcome') }}">
                    <x-athletegum-logo size="md" text-color="white" />
                </a>
                
                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center space-x-8">
                    <a href="{{ route('welcome') }}" class="text-white hover:text-white/80 transition text-sm font-medium">Home</a>
                    <a href="{{ route('pages.how-it-works') }}" class="text-white hover:text-white/80 transition text-sm font-medium">How It Works</a>
                    <a href="{{ route('pages.pricing') }}" class="text-white hover:text-white/80 transition text-sm font-medium">Pricing</a>
                    <a href="{{ route('pages.about') }}" class="text-white hover:text-white/80 transition text-sm font-medium">About</a>
                </nav>
                
                <!-- Desktop Right Side Actions -->
                <div class="hidden lg:flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-white hover:text-white/80 transition text-sm font-medium px-4 py-2 rounded-lg border border-white">Business Sign In</a>
                    <a href="{{ route('athlete.login') }}" class="text-white hover:text-white/80 transition text-sm font-medium px-4 py-2 rounded-lg border border-white">Athlete Sign In</a>
                </div>
                
                <!-- Mobile Menu Button -->
                <button @click="open = !open" class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="lg:hidden absolute top-16 left-0 right-0 bg-black border-b border-white/10 z-50" style="display: none;">
                <div class="px-4 pt-2 pb-4 space-y-1">
                    <a href="{{ route('welcome') }}" class="block px-3 py-2 text-sm font-medium rounded-md text-white">Home</a>
                    <a href="{{ route('pages.how-it-works') }}" class="block px-3 py-2 text-sm font-medium rounded-md text-white">How It Works</a>
                    <a href="{{ route('pages.pricing') }}" class="block px-3 py-2 text-sm font-medium rounded-md text-white">Pricing</a>
                    <a href="{{ route('pages.about') }}" class="block px-3 py-2 text-sm font-medium rounded-md text-white">About</a>
                    <div class="pt-4 space-y-2 border-t border-white/10">
                        <a href="{{ route('login') }}" class="block px-3 py-2 text-sm font-medium rounded-md text-center border border-white text-white">Business Sign In</a>
                        <a href="{{ route('athlete.login') }}" class="block px-3 py-2 text-sm font-medium rounded-md text-center border border-white text-white">Athlete Sign In</a>
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
        <footer class="bg-black border-t border-white/10 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <x-athletegum-logo size="sm" text-color="white" />
                    
                    <div class="flex flex-wrap justify-center gap-4 text-sm text-white/50">
                        <a href="{{ route('pages.about') }}" class="hover:text-white transition">About</a>
                        <span class="text-white/30">·</span>
                        <a href="{{ route('pages.terms') }}" class="hover:text-white transition">Terms</a>
                        <span class="text-white/30">·</span>
                        <a href="{{ route('pages.privacy') }}" class="hover:text-white transition">Privacy</a>
                        <span class="text-white/30">·</span>
                        <a href="{{ route('pages.contact') }}" class="hover:text-white transition">Contact</a>
                    </div>
                </div>
                
                <div class="mt-8 pt-8 border-t border-white/10 text-center text-xs text-white/40">
                    <p>&copy; {{ date('Y') }} AthleteGum. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </body>
</html>

