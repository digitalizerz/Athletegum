<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" style="color-scheme: light !important;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="color-scheme" content="light">
        <title>How It Works - AthleteGum</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23111'/><text x='50' y='65' font-size='50' font-weight='bold' fill='white' text-anchor='middle'>A</text></svg>" />
        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                <nav class="hidden lg:flex items-center space-x-8" style="background: transparent !important;">
                    <a href="{{ route('welcome') }}" class="text-sm font-medium" style="color: #ffffff !important; text-decoration: none !important; background: transparent !important;">Home</a>
                    <a href="{{ route('pages.how-it-works') }}" class="text-sm font-medium" style="color: #ffffff !important; text-decoration: none !important; background: transparent !important;">How It Works</a>
                    <a href="{{ route('pages.pricing') }}" class="text-sm font-medium" style="color: #ffffff !important; text-decoration: none !important; background: transparent !important;">Pricing</a>
                    <a href="{{ route('pages.about') }}" class="text-sm font-medium" style="color: #ffffff !important; text-decoration: none !important; background: transparent !important;">About</a>
                </nav>
                
                <!-- Desktop Right Side Actions -->
                <div class="hidden lg:flex items-center space-x-4" style="background: transparent !important;">
                    <a href="{{ route('login') }}" class="text-sm font-medium px-4 py-2 rounded-lg" style="color: #ffffff !important; border: 1px solid #ffffff !important; background: transparent !important; text-decoration: none !important;">Business Sign In</a>
                    <a href="{{ route('athlete.login') }}" class="text-sm font-medium px-4 py-2 rounded-lg" style="color: #ffffff !important; border: 1px solid #ffffff !important; background: transparent !important; text-decoration: none !important;">Athlete Sign In</a>
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
        <section class="py-16" style="background-color: #ffffff;">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4" style="color: #000000;">
                    How AthleteGum Works
                </h1>
                <p class="text-lg text-gray-700">
                    A simple, secure way to create NIL deals, deliver work, and release payment — only when the job is done.
                </p>
            </div>
        </section>

        <!-- For Businesses Section -->
        <section class="py-16" style="background-color: #ffffff;">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center" style="color: #000000;">For Businesses</h2>
                
                <div class="space-y-12">
                    <!-- Step 1 -->
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-black rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="mb-2">
                                <span class="text-sm font-semibold uppercase tracking-wide" style="color: #666666;">STEP 1</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4" style="color: #000000;">Create a Deal</h3>
                            <p class="text-gray-700 mb-4">Create a deal and define:</p>
                            <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                                <li>What work needs to be done</li>
                                <li>Deliverables</li>
                                <li>Timeline</li>
                                <li>Payment amount</li>
                            </ul>
                            <p class="text-gray-600 text-sm">Funds are secured upfront so athletes know the deal is real.</p>
                        </div>
                    </div>
                    
                    <!-- Step 2 -->
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-black rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="mb-2">
                                <span class="text-sm font-semibold uppercase tracking-wide" style="color: #666666;">STEP 2</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4" style="color: #000000;">Athlete Delivers</h3>
                            <p class="text-gray-700 mb-4">The athlete completes the work and submits deliverables directly on AthleteGum.</p>
                            <p class="text-gray-600 text-sm">Everything is documented — no chasing files or DMs.</p>
                        </div>
                    </div>
                    
                    <!-- Step 3 -->
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-black rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="mb-2">
                                <span class="text-sm font-semibold uppercase tracking-wide" style="color: #666666;">STEP 3</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4" style="color: #000000;">Review & Approve</h3>
                            <p class="text-gray-700 mb-4">You review the work and:</p>
                            <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2">
                                <li>Approve it, or</li>
                                <li>Request revisions</li>
                            </ul>
                            <p class="text-gray-600 text-sm">No payment is released until you approve the work.</p>
                        </div>
                    </div>
                    
                    <!-- Step 4 -->
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-black rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="mb-2">
                                <span class="text-sm font-semibold uppercase tracking-wide" style="color: #666666;">STEP 4</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4" style="color: #000000;">Payment Is Released</h3>
                            <p class="text-gray-700 mb-4">Once approved, payment is released automatically.</p>
                            <p class="text-gray-600 text-sm">Receipts and deal history are saved in your dashboard. Businesses never pay for incomplete work.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- For Athletes Section -->
        <section class="py-16" style="background-color: #ffffff;">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center" style="color: #000000;">For Athletes</h2>
                
                <div class="space-y-12">
                    <!-- Step 1 -->
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-black rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="mb-2">
                                <span class="text-sm font-semibold uppercase tracking-wide" style="color: #666666;">STEP 1</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4" style="color: #000000;">Join for Free</h3>
                            <p class="text-gray-700 mb-4">Create a free athlete profile.</p>
                            <p class="text-gray-600 text-sm">There are no signup fees and no subscriptions.</p>
                        </div>
                    </div>
                    
                    <!-- Step 2 -->
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-black rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="mb-2">
                                <span class="text-sm font-semibold uppercase tracking-wide" style="color: #666666;">STEP 2</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4" style="color: #000000;">Accept a Deal</h3>
                            <p class="text-gray-700 mb-4">Review deal terms, deliverables, and payment details before accepting.</p>
                            <p class="text-gray-600 text-sm">No surprises. Everything is clear upfront.</p>
                        </div>
                    </div>
                    
                    <!-- Step 3 -->
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-black rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="mb-2">
                                <span class="text-sm font-semibold uppercase tracking-wide" style="color: #666666;">STEP 3</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4" style="color: #000000;">Deliver the Work</h3>
                            <p class="text-gray-700 mb-4">Complete the work and upload deliverables directly to AthleteGum.</p>
                            <p class="text-gray-600 text-sm">If revisions are requested, you'll be notified inside the platform.</p>
                        </div>
                    </div>
                    
                    <!-- Step 4 -->
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-black rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="mb-2">
                                <span class="text-sm font-semibold uppercase tracking-wide" style="color: #666666;">STEP 4</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4" style="color: #000000;">Get Paid</h3>
                            <p class="text-gray-700 mb-4">Once the work is approved, payment is released automatically through Stripe.</p>
                            <p class="text-gray-600 text-sm">You control when and how you withdraw your earnings. Athletes always know when they'll get paid.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Secure Payments Section -->
        <section class="py-16" style="background-color: #f9fafb;">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl md:text-4xl font-bold mb-8 text-center" style="color: #000000;">Secure Payments, Built for Trust</h2>
                
                <div class="grid md:grid-cols-2 gap-8 items-start">
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <div class="w-2 h-2 rounded-full bg-black mt-2 mr-3 flex-shrink-0"></div>
                            <span class="text-gray-700">Funds are secured upfront</span>
                        </li>
                        <li class="flex items-start">
                            <div class="w-2 h-2 rounded-full bg-black mt-2 mr-3 flex-shrink-0"></div>
                            <span class="text-gray-700">Money is held until work is approved</span>
                        </li>
                        <li class="flex items-start">
                            <div class="w-2 h-2 rounded-full bg-black mt-2 mr-3 flex-shrink-0"></div>
                            <span class="text-gray-700">Payments are released automatically</span>
                        </li>
                        <li class="flex items-start">
                            <div class="w-2 h-2 rounded-full bg-black mt-2 mr-3 flex-shrink-0"></div>
                            <span class="text-gray-700">AthleteGum never touches athlete bank details</span>
                        </li>
                        <li class="flex items-start">
                            <div class="w-2 h-2 rounded-full bg-black mt-2 mr-3 flex-shrink-0"></div>
                            <span class="text-gray-700">All transactions are handled securely through Stripe</span>
                        </li>
                    </ul>
                    
                    <div class="bg-white border-2 border-black rounded-lg p-6">
                        <p class="text-gray-700 font-medium">This protects both sides — every time.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Revisions & Disputes Section -->
        <section class="py-16" style="background-color: #ffffff;">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center" style="color: #000000;">Revisions & Disputes</h2>
                
                <div class="space-y-8">
                    <div>
                        <div class="flex items-start mb-4">
                            <div class="w-6 h-6 rounded-full bg-black flex items-center justify-center mr-3 flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold mb-2" style="color: #000000;">What if revisions are needed?</h3>
                                <p class="text-gray-700">Businesses can request revisions before approving a deal. Athletes are notified and can resubmit updated work.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex items-start mb-4">
                            <div class="w-6 h-6 rounded-full bg-black flex items-center justify-center mr-3 flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold mb-2" style="color: #000000;">What if a deal isn't approved?</h3>
                                <p class="text-gray-700 mb-3">If work is not approved:</p>
                                <ul class="list-disc list-inside text-gray-700 mb-3 space-y-2">
                                    <li>Payment is not released</li>
                                    <li>No platform fee is charged</li>
                                </ul>
                                <p class="text-gray-600 text-sm">AthleteGum only earns when deals are completed successfully.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Fees Section -->
        <section class="py-16" style="background-color: #ffffff;">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center" style="color: #000000;">When does AthleteGum charge fees?</h2>
                
                <div class="space-y-6">
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-700">Businesses pay a platform fee only when a deal is completed.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-700 mb-2">Athletes never pay to join or use the platform.</p>
                            <p class="text-gray-600 text-sm">No subscriptions for athletes. No fees for incomplete work.</p>
                        </div>
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
                    <a href="{{ route('athlete.register') }}" class="inline-block font-semibold rounded-lg transition px-6 py-3" style="background-color: #ffffff; color: #000000; text-decoration: none;">
                        Create Athlete Profile
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12" style="background-color: #000000; border-top: 1px solid rgba(255, 255, 255, 0.1);">
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
