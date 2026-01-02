<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" style="color-scheme: light !important;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Force light mode on mobile browsers -->
        <meta name="color-scheme" content="light">
        <title>AthleteGum - Pay Athletes for Real Work</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23111'/><text x='50' y='65' font-size='50' font-weight='bold' fill='white' text-anchor='middle'>A</text></svg>" />
        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            // Prevent browser dark mode immediately on page load - MUST run before any styles
            (function() {
                // Force light color scheme
                document.documentElement.style.colorScheme = 'light';
                document.documentElement.setAttribute('data-theme', 'light');
                document.documentElement.classList.remove('dark', 'dark-mode');
                document.documentElement.classList.add('light');
                document.documentElement.style.backgroundColor = '#ffffff';
                
                // Prevent any dark mode classes
                if (document.body) {
                    document.body.classList.remove('dark', 'dark-mode');
                    document.body.style.colorScheme = 'light';
                }
                
                // Continuously monitor and prevent dark mode
                const observer = new MutationObserver(function(mutations) {
                    if (document.documentElement.classList.contains('dark') || 
                        document.documentElement.classList.contains('dark-mode') ||
                        document.documentElement.getAttribute('data-theme') === 'dark') {
                        document.documentElement.classList.remove('dark', 'dark-mode');
                        document.documentElement.setAttribute('data-theme', 'light');
                        document.documentElement.style.colorScheme = 'light';
                    }
                    if (document.body.classList.contains('dark') || 
                        document.body.classList.contains('dark-mode')) {
                        document.body.classList.remove('dark', 'dark-mode');
                        document.body.style.colorScheme = 'light';
                    }
                });
                
                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class', 'data-theme', 'style'],
                    childList: false,
                    subtree: false
                });
                
                observer.observe(document.body, {
                    attributes: true,
                    attributeFilter: ['class', 'style'],
                    childList: false,
                    subtree: false
                });
            })();
        </script>
    </head>
    <body class="font-sans antialiased" style="background-color: #ffffff; color: #000000;">
        <!-- Header -->
        <header x-data="{ open: false }" class="h-16 relative" style="background-color: #000000 !important; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between" style="background-color: #000000 !important;">
                <!-- Logo -->
                <a href="{{ route('welcome') }}" style="background: transparent !important;">
                    <x-athletegum-logo size="md" text-color="white" />
                </a>
                
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8" style="background: transparent !important;">
                    <a href="{{ route('welcome') }}" class="text-sm font-medium" style="color: #ffffff !important; text-decoration: none !important; background: transparent !important;">Home</a>
                    <a href="{{ route('pages.how-it-works') }}" class="text-sm font-medium" style="color: #ffffff !important; text-decoration: none !important; background: transparent !important;">How It Works</a>
                    <a href="{{ route('pages.pricing') }}" class="text-sm font-medium" style="color: #ffffff !important; text-decoration: none !important; background: transparent !important;">Pricing</a>
                    <a href="{{ route('pages.about') }}" class="text-sm font-medium" style="color: #ffffff !important; text-decoration: none !important; background: transparent !important;">About</a>
                </nav>
                
                <!-- Desktop Right Side Actions -->
                <div class="hidden md:flex items-center space-x-4" style="background: transparent !important;">
                    <a href="{{ route('login') }}" class="text-sm font-medium px-4 py-2 rounded-lg" style="color: #ffffff !important; border: 1px solid #ffffff !important; background: transparent !important; text-decoration: none !important;">Business Sign In</a>
                    <a href="{{ route('athlete.login') }}" class="text-sm font-medium px-4 py-2 rounded-lg" style="color: #ffffff !important; border: 1px solid #ffffff !important; background: transparent !important; text-decoration: none !important;">Athlete Sign In</a>
                </div>
                
                <!-- Mobile Menu Button -->
                <button @click="open = !open" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="md:hidden absolute top-16 left-0 right-0 bg-black border-b border-white/10 z-50" style="display: none;">
                <div class="px-4 pt-2 pb-4 space-y-1">
                    <a href="{{ route('welcome') }}" class="block px-3 py-2 text-sm font-medium rounded-md" style="color: #ffffff !important; text-decoration: none !important;">Home</a>
                    <a href="{{ route('pages.how-it-works') }}" class="block px-3 py-2 text-sm font-medium rounded-md" style="color: #ffffff !important; text-decoration: none !important;">How It Works</a>
                    <a href="{{ route('pages.pricing') }}" class="block px-3 py-2 text-sm font-medium rounded-md" style="color: #ffffff !important; text-decoration: none !important;">Pricing</a>
                    <a href="{{ route('pages.about') }}" class="block px-3 py-2 text-sm font-medium rounded-md" style="color: #ffffff !important; text-decoration: none !important;">About</a>
                    <div class="pt-4 space-y-2 border-t border-white/10">
                        <a href="{{ route('login') }}" class="block px-3 py-2 text-sm font-medium rounded-md text-center border border-white" style="color: #ffffff !important; text-decoration: none !important;">Business Sign In</a>
                        <a href="{{ route('athlete.login') }}" class="block px-3 py-2 text-sm font-medium rounded-md text-center border border-white" style="color: #ffffff !important; text-decoration: none !important;">Athlete Sign In</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="bg-black" style="padding-top: 72px; padding-bottom: 56px; background-color: #000000;">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6" style="color: #ffffff;">
                    Pay Athletes for Real Work — Safely and Transparently
                </h1>
                <p class="text-lg md:text-xl mb-8 max-w-2xl mx-auto" style="color: rgba(255, 255, 255, 0.8);">
                    Create NIL deals, secure funds upfront, and only release payment after work is delivered and approved.
                </p>
                
                <!-- Primary CTAs -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-6">
                    <a href="{{ route('register') }}" class="inline-block bg-white text-black font-semibold rounded-lg hover:bg-white/90 transition px-6 py-3" style="background-color: #ffffff; color: #000000;">
                        Create Business Account
                    </a>
                    <a href="{{ route('athlete.register') }}" class="inline-block bg-white text-black font-semibold rounded-lg hover:bg-white/90 transition px-6 py-3" style="background-color: #ffffff; color: #000000;">
                        Create Athlete Profile
                    </a>
                </div>
                
                <!-- Trust Line -->
                <p class="text-sm" style="color: rgba(255, 255, 255, 0.6);">
                    Secure payments • Funds held in escrow • No marketplace fees
                </p>
            </div>
        </section>

        <!-- Introduction Section -->
        <section class="bg-white py-16">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-black mb-4">
                    Built for modern NIL deals — trusted by businesses, athletes, and collectives.
                </h2>
                <p class="text-lg text-gray-700">
                    For brands, agencies, creators and athletes who want clarity, protection, and fair payment.
                </p>
            </div>
        </section>

        <!-- How It Works Section -->
        <section id="how-it-works" class="bg-white py-16">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-black mb-4">How It Works</h2>
                </div>
                
                <div class="grid md:grid-cols-3 gap-8 mb-8">
                    <!-- Step 1 -->
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center rounded-full border-2 border-black mb-4" style="width: 56px; height: 56px;">
                            <span class="text-xl font-bold text-black">1</span>
                        </div>
                        <h3 class="text-lg font-semibold text-black mb-3">Create a Deal</h3>
                        <p class="text-gray-700 text-sm leading-relaxed">
                            Define deliverables and funding it upfront, with funds held in escrow.
                        </p>
                    </div>
                    
                    <!-- Step 2 -->
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center rounded-full border-2 border-black mb-4" style="width: 56px; height: 56px;">
                            <span class="text-xl font-bold text-black">2</span>
                        </div>
                        <h3 class="text-lg font-semibold text-black mb-3">Athlete Delivers</h3>
                        <p class="text-gray-700 text-sm leading-relaxed">
                            The athlete completes work and delivers directly on AthleteGum.
                        </p>
                    </div>
                    
                    <!-- Step 3 -->
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center rounded-full border-2 border-black mb-4" style="width: 56px; height: 56px;">
                            <span class="text-xl font-bold text-black">3</span>
                        </div>
                        <h3 class="text-lg font-semibold text-black mb-3">Review & Pay</h3>
                        <p class="text-gray-700 text-sm leading-relaxed">
                            Review the work and approving payment to be released automatically.
                        </p>
                    </div>
                </div>
                
                <p class="text-center text-xs text-gray-500 mb-4">
                    AthleteGum only pays out when the deal is completed.
                </p>
                <div class="text-center">
                    <a href="#how-it-works" class="text-sm text-gray-600 hover:text-black transition inline-flex items-center">
                        See how it works in detail
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- Built for Real NIL Work Section -->
        <section class="bg-gray-50 py-16">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-black mb-4">Built for Real NIL Work — Not Marketplaces</h2>
                </div>
                
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-black mb-2">Escrow Style Payments</h3>
                        <p class="text-gray-700 text-sm">
                            Funds are protected until work is delivered and approved.
                        </p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-black mb-2">Clear Deliverables</h3>
                        <p class="text-gray-700 text-sm">
                            Every deal defines exact deliverables, no disputes.
                        </p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-black mb-2">Fair for Both Sides</h3>
                        <p class="text-gray-700 text-sm">
                            Business doesn't pay for incomplete work. Athletes get paid on time.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Choose Your Path Section -->
        <section class="bg-white py-16">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-black mb-4">Choose Your Path</h2>
                </div>
                
                <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    <!-- Business Card -->
                    <div class="bg-white rounded-lg border-2 border-black p-6 hover:shadow-lg transition">
                        <h3 class="text-xl font-bold text-black mb-3">For Businesses</h3>
                        <p class="text-gray-700 text-sm mb-6 leading-relaxed">
                            Create deals, review work, and release payments with confidence.
                        </p>
                        
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-black mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-gray-700 text-sm">Secure upfront funding</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-black mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-gray-700 text-sm">Clear deliverables</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-black mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-gray-700 text-sm">Approval before payment</span>
                            </li>
                        </ul>
                        
                        <a href="{{ route('register') }}" class="block w-full text-center bg-black text-white rounded-lg font-semibold hover:bg-black/90 transition py-3">
                            Create Business Account
                        </a>
                    </div>

                    <!-- Athlete Card -->
                    <div class="bg-white rounded-lg border-2 border-black p-6 hover:shadow-lg transition">
                        <h3 class="text-xl font-bold text-black mb-3">For Athletes</h3>
                        <p class="text-gray-700 text-sm mb-6 leading-relaxed">
                            Accept deals, submit work, and get paid for what you do.
                        </p>
                        
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-black mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-gray-700 text-sm">No chasing payments</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-black mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-gray-700 text-sm">Full payout control</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-black mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-gray-700 text-sm">Transparent deal terms</span>
                            </li>
                        </ul>
                        
                        <a href="{{ route('athlete.register') }}" class="block w-full text-center bg-black text-white rounded-lg font-semibold hover:bg-black/90 transition py-3">
                            Create Athlete Profile
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Everything You Need Section -->
        <section class="bg-gray-50 py-16">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-black mb-4">Everything You Need to Run NIL Deals Properly</h2>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-700">Clear NIL deliverables</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-700">Secure escrow style payments</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-700">Work submission & file uploads</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-700">Review, accept, and reject work</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-700">Automated payments</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-700">Payment history & receipts</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-700">Online payout management</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-700">Deal term enforcement</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Built for Trust Section -->
        <section class="bg-white py-16">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-black mb-4">Built for Trust, Compliance, and Peace of Mind</h2>
                </div>
                
                <div class="space-y-3 mb-8">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-gray-700">Funds are held until work is approved</p>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-gray-700">Payments are handled securely</p>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-gray-700">Athletes control their own payouts</p>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-gray-700">Businesses never pay for incomplete work</p>
                    </div>
                </div>
                
                <p class="text-center text-sm text-gray-600">
                    AthleteGum never touches athlete bank details.
                </p>
            </div>
        </section>

        <!-- Who It's For Section -->
        <section class="bg-gray-50 py-16">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-black mb-4">Who It's For</h2>
                </div>
                
                <div class="grid md:grid-cols-2 gap-12 max-w-4xl mx-auto">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-black rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-black">For Businesses</h3>
                        </div>
                        <ul class="space-y-2 text-gray-700 text-sm">
                            <li>Small businesses</li>
                            <li>Local brands</li>
                            <li>Agencies & collectives</li>
                            <li>One-off or recurring NIL campaigns</li>
                        </ul>
                    </div>
                    
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-black rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-black">For Athletes</h3>
                        </div>
                        <ul class="space-y-2 text-gray-700 text-sm">
                            <li>College athletes</li>
                            <li>NIL-eligible creators</li>
                            <li>Influencers</li>
                            <li>Individuals or teams</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA Section -->
        <section class="bg-black py-16">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-white">
                    Start Your First Deal in Minutes
                </h2>
                <p class="text-lg mb-8" style="color: #ffffff !important;">
                    Create a deal, fund it securely, and pay only when the work is done.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-4">
                    <a href="{{ route('register') }}" class="inline-block bg-white text-black font-semibold rounded-lg hover:bg-white/90 transition px-6 py-3" style="background-color: #ffffff; color: #000000;">
                        Create Business Account
                    </a>
                    <a href="{{ route('athlete.register') }}" class="inline-block bg-white text-black font-semibold rounded-lg hover:bg-white/90 transition px-6 py-3" style="background-color: #ffffff; color: #000000;">
                        Create Athlete Profile
                    </a>
                </div>
                
                <p class="text-sm" style="color: #ffffff !important;">
                    No contracts. No marketplace. Just real work.
                </p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-black border-t border-white/10 py-12" style="background-color: #000000;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <x-athletegum-logo size="sm" text-color="white" />
                    
                    <div class="flex flex-wrap justify-center gap-4 text-sm">
                        <a href="{{ route('pages.about') }}" class="transition" style="color: #ffffff !important;">About</a>
                        <span style="color: rgba(255, 255, 255, 0.5) !important;">·</span>
                        <a href="{{ route('pages.terms') }}" class="transition" style="color: #ffffff !important;">Terms</a>
                        <span style="color: rgba(255, 255, 255, 0.5) !important;">·</span>
                        <a href="{{ route('pages.privacy') }}" class="transition" style="color: #ffffff !important;">Privacy</a>
                        <span style="color: rgba(255, 255, 255, 0.5) !important;">·</span>
                        <a href="{{ route('pages.contact') }}" class="transition" style="color: #ffffff !important;">Contact</a>
                    </div>
                </div>
                
                <div class="mt-8 pt-8 text-center text-xs" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                    <p style="color: #ffffff !important;">&copy; {{ date('Y') }} AthleteGum. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </body>
</html>