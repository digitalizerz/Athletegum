<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Terms of Service - AthleteGum</title>
        
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
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Terms of Service</h1>
                <p class="text-gray-600 mb-12">Last updated: {{ date('F j, Y') }}</p>
                
                <div class="prose prose-lg max-w-none">
                    <p class="text-gray-700 mb-12 leading-relaxed">
                        By using AthleteGum, you agree to the following terms. Please read them carefully.
                    </p>

                    <h2 class="text-2xl font-bold mb-4 mt-12">1. Platform Overview</h2>
                    <p class="text-gray-700 mb-6 leading-relaxed">
                        AthleteGum provides a platform for businesses and athletes to create, manage, and complete NIL-related deals.
                    </p>
                    <p class="text-gray-700 mb-12 leading-relaxed">
                        AthleteGum is not a party to the agreement between businesses and athletes. We provide tools to manage deals, deliverables, and payments.
                    </p>

                    <h2 class="text-2xl font-bold mb-4 mt-12">2. User Accounts</h2>
                    <p class="text-gray-700 mb-4 leading-relaxed">
                        You must provide accurate information when creating an account.
                    </p>
                    <p class="text-gray-700 mb-4 leading-relaxed">
                        You are responsible for:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-12 space-y-2 ml-4">
                        <li>Keeping your login credentials secure</li>
                        <li>All activity under your account</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-4 mt-12">3. Deals and Deliverables</h2>
                    <p class="text-gray-700 mb-4 leading-relaxed">
                        Businesses define deliverables when creating a deal.
                    </p>
                    <p class="text-gray-700 mb-4 leading-relaxed">
                        Athletes are responsible for completing and submitting deliverables as described.
                    </p>
                    <p class="text-gray-700 mb-12 leading-relaxed">
                        Businesses must review submissions in good faith and respond in a timely manner.
                    </p>

                    <h2 class="text-2xl font-bold mb-4 mt-12">4. Payments</h2>
                    <ul class="list-disc list-inside text-gray-700 mb-12 space-y-2 ml-4">
                        <li>Funds are funded upfront by businesses</li>
                        <li>Payments are released only after work is approved</li>
                        <li>AthleteGum charges platform fees as disclosed in the app</li>
                        <li>Athletes receive payouts after fees are deducted</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-4 mt-12">5. Prohibited Use</h2>
                    <p class="text-gray-700 mb-4 leading-relaxed">
                        You may not:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-12 space-y-2 ml-4">
                        <li>Use AthleteGum for illegal activity</li>
                        <li>Misrepresent identity or work</li>
                        <li>Abuse the review or revision process</li>
                        <li>Attempt to bypass platform payments</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-4 mt-12">6. Account Suspension</h2>
                    <p class="text-gray-700 mb-12 leading-relaxed">
                        AthleteGum may suspend or terminate accounts that violate these terms.
                    </p>

                    <h2 class="text-2xl font-bold mb-4 mt-12">7. Limitation of Liability</h2>
                    <p class="text-gray-700 mb-4 leading-relaxed">
                        AthleteGum is not responsible for:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-12 space-y-2 ml-4">
                        <li>Disputes outside the platform</li>
                        <li>Off-platform agreements</li>
                        <li>Content created by users</li>
                    </ul>

                    <h2 class="text-2xl font-bold mb-4 mt-12">8. Changes to Terms</h2>
                    <p class="text-gray-700 mb-12 leading-relaxed">
                        We may update these terms from time to time. Continued use of the platform means you accept the updated terms.
                    </p>

                    <h2 class="text-2xl font-bold mb-4 mt-12">9. Contact</h2>
                    <p class="text-gray-700 mb-8 leading-relaxed">
                        Questions about these terms?
                    </p>
                    <p class="text-gray-700 mb-12 leading-relaxed">
                        Contact us at <a href="mailto:business@athletegum.com" class="text-black hover:underline">business@athletegum.com</a>
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

