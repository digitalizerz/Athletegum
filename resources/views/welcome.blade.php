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
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-black text-white">
        <!-- Header -->
        <header class="bg-black border-b border-white/10 h-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between">
                <a href="{{ route('welcome') }}">
                    <x-athletegum-logo size="md" text-color="white" />
                </a>
                <div class="relative" x-data="{ open: false }">
                    <button 
                        @click="open = !open"
                        class="px-4 py-2 border border-white/20 rounded-lg hover:border-white/40 transition text-sm font-medium"
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

        <!-- Hero Section -->
        <section class="bg-black" style="padding-top: 72px; padding-bottom: 56px;">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-4">
                    Pay Athletes for Real Work<br />
                    <span class="text-white/90">Safely and Transparently</span>
                </h1>
                <p class="text-lg text-white/70 mb-6 max-w-2xl mx-auto">
                    Create NIL deals, hold funds securely, and only pay athletes after work is delivered.
                </p>
                
                <!-- Primary CTAs -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-3" style="gap: 16px;">
                    <a href="{{ route('register') }}" class="inline-block bg-white text-black font-semibold rounded-lg hover:bg-white/90 transition" style="max-width: 260px; width: 100%; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                        Create Business Account
                    </a>
                    <a href="{{ route('athlete.register') }}" class="inline-block bg-white text-black font-semibold rounded-lg hover:bg-white/90 transition" style="max-width: 260px; width: 100%; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                        Create Athlete Profile
                    </a>
                </div>
                
                <!-- Trust Line -->
                <p class="text-sm text-white/50">
                    Secure payments • Funds held until work is approved • No marketplace noise
                </p>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="bg-white" style="padding-top: 48px; padding-bottom: 56px;">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-black mb-4">How It Works</h2>
                </div>
                
                <div class="grid md:grid-cols-3 gap-10">
                    <!-- Step 1 -->
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center rounded-full border-2 border-black mb-2" style="width: 56px; height: 56px; padding: 0;">
                            <span class="text-xl font-bold text-black">1</span>
                        </div>
                        <h3 class="text-lg font-semibold text-black mb-2">Create a Deal</h3>
                        <p class="text-gray-700 text-sm leading-relaxed" style="line-height: 1.4;">
                            Create a deal, define what needs to be done, and fund it upfront. AthleteGum holds the money securely.
                        </p>
                    </div>
                    
                    <!-- Step 2 -->
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center rounded-full border-2 border-black mb-2" style="width: 56px; height: 56px; padding: 0;">
                            <span class="text-xl font-bold text-black">2</span>
                        </div>
                        <h3 class="text-lg font-semibold text-black mb-2">Athlete Delivers</h3>
                        <p class="text-gray-700 text-sm leading-relaxed" style="line-height: 1.4;">
                            The athlete completes the work and submits deliverables directly in AthleteGum.
                        </p>
                    </div>
                    
                    <!-- Step 3 -->
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center rounded-full border-2 border-black mb-2" style="width: 56px; height: 56px; padding: 0;">
                            <span class="text-xl font-bold text-black">3</span>
                        </div>
                        <h3 class="text-lg font-semibold text-black mb-2">Review & Pay</h3>
                        <p class="text-gray-700 text-sm leading-relaxed" style="line-height: 1.4;">
                            Review the work. When approved, payment is released automatically.
                        </p>
                    </div>
                </div>
                
                <p class="text-center text-xs text-gray-500 mt-4">
                    AthleteGum only gets paid when a deal is completed.
                </p>
            </div>
        </section>

        <!-- Split Path Section -->
        <section class="bg-white border-t border-gray-200" style="padding-top: 56px; padding-bottom: 56px;">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-black mb-4">Choose your path</h2>
                </div>
                
                <div class="grid md:grid-cols-2 gap-8 max-w-3xl mx-auto" style="gap: 32px;">
                    <!-- Business Card -->
                    <div class="bg-white rounded-lg border-2 border-black hover:shadow-lg transition" style="max-width: 420px; padding: 24px; margin: 0 auto;">
                        <div class="mb-2">
                            <h3 class="text-xl font-bold text-black mb-2">I'm a Business</h3>
                            <p class="text-gray-700 text-sm leading-relaxed mb-4" style="line-height: 1.4;">
                                Create deals, review work, and release payments with confidence.
                            </p>
                        </div>

                        <div class="mb-2">
                            <a href="{{ route('register') }}" class="block w-full text-center bg-black text-white rounded-lg font-semibold hover:bg-black/90 transition mb-2" style="padding: 12px 16px;">
                                Create Business Account
                            </a>
                            <a href="{{ route('login') }}" class="block w-full text-center font-normal mb-2" style="font-size: 14px; color: #6b7280; text-decoration: none;">
                                Sign in
                            </a>
                        </div>

                        <p class="text-xs text-gray-500 text-center mt-2">
                            For brands, agencies, collectives, and SMBs
                        </p>
                    </div>

                    <!-- Athlete Card -->
                    <div class="bg-white rounded-lg border-2 border-black hover:shadow-lg transition" style="max-width: 420px; padding: 24px; margin: 0 auto;">
                        <div class="mb-2">
                            <h3 class="text-xl font-bold text-black mb-2">I'm an Athlete</h3>
                            <p class="text-gray-700 text-sm leading-relaxed mb-4" style="line-height: 1.4;">
                                Accept deals, submit work, and get paid for what you do.
                            </p>
                        </div>

                        <div class="mb-2">
                            <a href="{{ route('athlete.register') }}" class="block w-full text-center bg-black text-white rounded-lg font-semibold hover:bg-black/90 transition mb-2" style="padding: 12px 16px;">
                                Create Athlete Profile
                            </a>
                            <a href="{{ route('athlete.login') }}" class="block w-full text-center font-normal mb-2" style="font-size: 14px; color: #6b7280; text-decoration: none;">
                                Sign in
                            </a>
                        </div>

                        <p class="text-xs text-gray-500 text-center mt-2">
                            Free profile • Get paid securely
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trust Section -->
        <section class="bg-white border-t border-gray-200" style="padding-top: 48px; padding-bottom: 48px;">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-black mb-4">Built for Trust</h2>
                </div>
                
                <div class="text-gray-700 mb-3" style="line-height: 1.4;">
                    <div class="flex items-start gap-3 mb-2">
                        <div class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-black mt-2"></div>
                        <p>Funds are held until work is approved</p>
                    </div>
                    <div class="flex items-start gap-3 mb-2">
                        <div class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-black mt-2"></div>
                        <p>Payments are handled securely</p>
                    </div>
                    <div class="flex items-start gap-3 mb-2">
                        <div class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-black mt-2"></div>
                        <p>Athletes control their own payouts</p>
                    </div>
                    <div class="flex items-start gap-3 mb-2">
                        <div class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-black mt-2"></div>
                        <p>Businesses never pay for incomplete work</p>
                    </div>
                </div>
                
                <p class="text-center text-xs text-gray-500 mt-3">
                    AthleteGum never touches athlete bank details.
                </p>
            </div>
        </section>

        <!-- Who It's For Section -->
        <section class="bg-white border-t border-gray-200" style="padding-top: 40px; padding-bottom: 40px;">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-black mb-4">Who It's For</h2>
                </div>
                
                <div class="grid md:grid-cols-2 max-w-2xl mx-auto" style="gap: 48px;">
                    <div style="max-width: 360px;">
                        <h3 class="text-lg font-semibold text-black mb-4">For Businesses</h3>
                        <ul class="space-y-2 text-gray-700 text-sm">
                            <li>Small businesses</li>
                            <li>Local brands</li>
                            <li>Agencies & collectives</li>
                            <li>One-off or recurring campaigns</li>
                        </ul>
                    </div>
                    <div style="max-width: 360px;">
                        <h3 class="text-lg font-semibold text-black mb-4">For Athletes</h3>
                        <ul class="space-y-2 text-gray-700 text-sm">
                            <li>College athletes</li>
                            <li>Creators & influencers</li>
                            <li>Individual or team deals</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA Section -->
        <section class="bg-black" style="padding-top: 56px; padding-bottom: 56px;">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-8">
                    Start your first deal in minutes
                </h2>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-3" style="gap: 16px;">
                    <a href="{{ route('register') }}" class="inline-block bg-white text-black font-semibold rounded-lg hover:bg-white/90 transition" style="max-width: 260px; width: 100%; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                        Create Business Account
                    </a>
                    <a href="{{ route('athlete.register') }}" class="inline-block bg-white text-black font-semibold rounded-lg hover:bg-white/90 transition" style="max-width: 260px; width: 100%; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                        Create Athlete Profile
                    </a>
                </div>
                
                <p class="text-sm text-white/50">
                    No contracts. No marketplace noise. Just real deals.
                </p>
            </div>
        </section>

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
