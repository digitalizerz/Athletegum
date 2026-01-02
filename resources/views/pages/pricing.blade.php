<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" style="color-scheme: light !important;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="color-scheme" content="light">
        <title>Pricing - AthleteGum</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23111'/><text x='50' y='65' font-size='50' font-weight='bold' fill='white' text-anchor='middle'>A</text></svg>" />
        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" style="background-color: #ffffff; color: #000000;">
        <!-- Header -->
        <header class="h-16" style="background-color: #000000 !important; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between" style="background-color: #000000 !important;">
                <!-- Logo -->
                <a href="{{ route('welcome') }}" style="background: transparent !important;">
                    <x-athletegum-logo size="md" text-color="white" />
                </a>
                
                <!-- Navigation -->
                <nav class="flex items-center space-x-8" style="display: flex !important; background: transparent !important;">
                    <a href="{{ route('welcome') }}" class="text-sm font-medium" style="color: #ffffff !important; text-decoration: none !important; background: transparent !important; display: inline-block;">Home</a>
                    <a href="{{ route('pages.how-it-works') }}" class="text-sm font-medium" style="color: #ffffff !important; text-decoration: none !important; background: transparent !important; display: inline-block;">How It Works</a>
                    <a href="{{ route('pages.pricing') }}" class="text-sm font-medium" style="color: #ffffff !important; text-decoration: none !important; background: transparent !important; display: inline-block;">Pricing</a>
                    <a href="{{ route('pages.about') }}" class="text-sm font-medium" style="color: #ffffff !important; text-decoration: none !important; background: transparent !important; display: inline-block;">About</a>
                </nav>
                
                <!-- Right Side Actions -->
                <div class="flex items-center space-x-4" style="background: transparent !important;">
                    <a href="{{ route('login') }}" class="text-sm font-medium px-4 py-2 rounded-lg" style="color: #ffffff !important; border: 1px solid #ffffff !important; background: transparent !important; text-decoration: none !important;">Business Sign In</a>
                    <a href="{{ route('athlete.login') }}" class="text-sm font-medium px-4 py-2 rounded-lg" style="color: #ffffff !important; border: 1px solid #ffffff !important; background: transparent !important; text-decoration: none !important;">Athlete Sign In</a>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="py-16" style="background-color: #ffffff;">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4" style="color: #000000;">
                    Simple, Fair Pricing for NIL Deals
                </h1>
                <p class="text-lg text-gray-700">
                    Athletes join for free. Businesses choose a plan based on deal volume and features.
                </p>
            </div>
        </section>

        <!-- For Athletes - Always Free Section -->
        <section class="py-16" style="background-color: #ffffff;">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl md:text-4xl font-bold mb-8 text-center" style="color: #000000;">For Athletes — Always Free</h2>
                
                <div class="max-w-md mx-auto">
                    <div class="bg-white border-2 border-gray-200 rounded-lg p-8">
                        <h3 class="text-2xl font-bold mb-2" style="color: #000000;">Free</h3>
                        <p class="text-gray-700 mb-6">Athletes never pay to use AthleteGum.</p>
                        
                        <div class="mb-6">
                            <p class="text-sm font-semibold mb-3" style="color: #000000;">What athletes get</p>
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">Create a free athlete profile</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">Accept NIL deals</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">Clear deal terms & deliverables</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">Submit work & revisions</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">Secure payments through Stripe</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">Full payout control & earnings history</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                            <p class="text-sm text-gray-700">Athletes keep 100% of their deal earnings. AthleteGum never charges athletes to join or participate.</p>
                        </div>
                        
                        <a href="{{ route('athlete.register') }}" class="block w-full text-center font-semibold rounded-lg transition py-3" style="background-color: #000000; color: #ffffff; text-decoration: none;">
                            Create Athlete Profile
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- For Businesses - Plans Section -->
        <section class="py-16" style="background-color: #ffffff;">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: #000000;">For Businesses — Plans</h2>
                    <p class="text-lg text-gray-700">Choose the plan that fits your deal volume and workflow.</p>
                </div>
                
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Free Plan -->
                    <div class="bg-white border-2 border-gray-200 rounded-lg p-8">
                        <h3 class="text-2xl font-bold mb-2" style="color: #000000;">Free</h3>
                        <p class="text-3xl font-bold mb-1" style="color: #000000;">$0<span class="text-lg font-normal text-gray-600"> / month</span></p>
                        <p class="text-gray-700 mb-6 text-sm">For businesses just getting started.</p>
                        
                        <div class="mb-6">
                            <p class="text-sm font-semibold mb-3" style="color: #000000;">Includes</p>
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">Create up to 3 active deals</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">Secure upfront funding</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">Escrow-style payment protection</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">Deliverables & approval workflow</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">Automated payouts</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">Basic payment history</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="mb-6 pt-6 border-t border-gray-200">
                            <p class="text-sm text-gray-600 mb-1">Platform fee</p>
                            <p class="text-lg font-semibold" style="color: #000000;">10% per completed deal</p>
                        </div>
                        
                        <a href="{{ route('register') }}" class="block w-full text-center font-semibold rounded-lg transition py-3" style="background-color: #000000; color: #ffffff; text-decoration: none;">
                            Get Started Free
                        </a>
                    </div>
                    
                    <!-- Pro Plan -->
                    <div class="bg-white border-2 border-black rounded-lg p-8 relative">
                        <div class="absolute top-0 left-0 right-0 bg-black text-white text-xs font-semibold py-2 px-4 rounded-t-lg text-center">
                            MOST POPULAR
                        </div>
                        <div class="pt-8">
                            <h3 class="text-2xl font-bold mb-2" style="color: #000000;">Pro</h3>
                            <p class="text-3xl font-bold mb-1" style="color: #000000;">$49<span class="text-lg font-normal text-gray-600"> / month</span></p>
                            <p class="text-gray-700 mb-6 text-sm">For growing brands running ongoing NIL campaigns.</p>
                            
                            <div class="mb-6">
                                <p class="text-sm font-semibold mb-3" style="color: #000000;">Includes everything in Free, plus</p>
                                <ul class="space-y-3">
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-gray-700 text-sm">Unlimited deals</span>
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-gray-700 text-sm">Lower platform fee</span>
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-gray-700 text-sm">Advanced deal tracking</span>
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-gray-700 text-sm">Downloadable receipts & reports</span>
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-gray-700 text-sm">Priority support</span>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="mb-6 pt-6 border-t border-gray-200">
                                <p class="text-sm text-gray-600 mb-1">Platform fee</p>
                                <p class="text-lg font-semibold" style="color: #000000;">8% per completed deal</p>
                            </div>
                            
                            <a href="{{ route('subscriptions.checkout', 'pro') }}" class="block w-full text-center font-semibold rounded-lg transition py-3" style="background-color: #000000; color: #ffffff; text-decoration: none;">
                                Upgrade to Pro
                            </a>
                        </div>
                    </div>
                    
                    <!-- Growth Plan -->
                    <div class="bg-white border-2 border-gray-200 rounded-lg p-8">
                        <h3 class="text-2xl font-bold mb-2" style="color: #000000;">Growth</h3>
                        <p class="text-3xl font-bold mb-1" style="color: #000000;">$99<span class="text-lg font-normal text-gray-600"> / month</span></p>
                        <p class="text-gray-700 mb-6 text-sm">For agencies, collectives, and high-volume teams.</p>
                        
                        <div class="mb-6">
                            <p class="text-sm font-semibold mb-3" style="color: #000000;">Includes everything in Pro, plus</p>
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">Lowest platform fee</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">Team & role management</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">Deal templates</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">Volume-friendly workflows</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-black mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">Dedicated support</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="mb-6 pt-6 border-t border-gray-200">
                            <p class="text-sm text-gray-600 mb-1">Platform fee</p>
                            <p class="text-lg font-semibold" style="color: #000000;">6% per completed deal</p>
                        </div>
                        
                        <a href="{{ route('subscriptions.checkout', 'growth') }}" class="block w-full text-center font-semibold rounded-lg transition py-3" style="background-color: #000000; color: #ffffff; text-decoration: none;">
                            Upgrade to Growth
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- No Surprises Section -->
        <section class="py-16" style="background-color: #ffffff;">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center" style="color: #000000;">No surprises. No lock-in.</h2>
                
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2" style="color: #000000;">No contracts</h3>
                            <p class="text-gray-700 text-sm">Cancel anytime.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2" style="color: #000000;">Platform fees only apply when deals are completed</h3>
                            <p class="text-gray-700 text-sm">No fees if work is not approved.</p>
                        </div>
                    </div>
                </div>
                
                <p class="text-center text-gray-700">AthleteGum only earns when deals are successful.</p>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="py-16" style="background-color: #ffffff;">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center" style="color: #000000;">FAQ</h2>
                
                <div class="space-y-8">
                    <div>
                        <h3 class="text-xl font-bold mb-3" style="color: #000000;">Do athletes ever pay?</h3>
                        <p class="text-gray-700">No. Athletes use AthleteGum for free.</p>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-bold mb-3" style="color: #000000;">Can I switch plans anytime?</h3>
                        <p class="text-gray-700">Yes. You can upgrade or downgrade at any time.</p>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-bold mb-3" style="color: #000000;">What happens if I exceed Free limits?</h3>
                        <p class="text-gray-700">You'll be prompted to upgrade, but existing deals are never blocked mid-flow.</p>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-bold mb-3" style="color: #000000;">What if work isn't approved?</h3>
                        <p class="text-gray-700">Funds are not released and no platform fee is charged.</p>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-bold mb-3" style="color: #000000;">Will you add enterprise plans later?</h3>
                        <p class="text-gray-700">Yes. Custom enterprise plans will be available for large organizations.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA Section -->
        <section class="py-16" style="background-color: #000000;">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: #ffffff;">
                    Start Your First Deal with Confidence
                </h2>
                <p class="text-lg mb-8" style="color: #ffffff;">
                    Create a deal, fund it securely, and pay only when the work is done.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-4">
                    <a href="{{ route('register') }}" class="inline-block font-semibold rounded-lg transition px-6 py-3" style="background-color: #ffffff; color: #000000; text-decoration: none;">
                        Create Business Account
                    </a>
                    <a href="{{ route('athlete.register') }}" class="inline-block font-semibold rounded-lg transition px-6 py-3" style="background-color: #000000; color: #ffffff; border: 1px solid #ffffff; text-decoration: none;">
                        Create Athlete Profile
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12" style="background-color: #ffffff; border-top: 1px solid rgba(0, 0, 0, 0.1);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <x-athletegum-logo size="sm" text-color="default" />
                    
                    <div class="flex flex-col md:flex-row items-center gap-4">
                        <p class="text-xs text-gray-600">&copy; {{ date('Y') }} AthleteGum. All rights reserved.</p>
                        <div class="flex flex-wrap justify-center gap-4 text-sm">
                            <a href="{{ route('pages.about') }}" class="transition" style="color: #000000 !important; text-decoration: none;">About</a>
                            <span style="color: rgba(0, 0, 0, 0.3);">·</span>
                            <a href="{{ route('pages.privacy') }}" class="transition" style="color: #000000 !important; text-decoration: none;">Privacy</a>
                            <span style="color: rgba(0, 0, 0, 0.3);">·</span>
                            <a href="{{ route('pages.terms') }}" class="transition" style="color: #000000 !important; text-decoration: none;">Terms</a>
                            <span style="color: rgba(0, 0, 0, 0.3);">·</span>
                            <a href="{{ route('pages.contact') }}" class="transition" style="color: #000000 !important; text-decoration: none;">Contact</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>