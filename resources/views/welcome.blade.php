<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>AthleteGum - Pay Athletes for Real Work</title>

        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23111'/><text x='50' y='65' font-size='50' font-weight='bold' fill='white' text-anchor='middle'>A</text></svg>" />
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased bg-white text-black">
        {{-- Header --}}
        <header class="fixed inset-x-0 top-0 z-50 w-full border-b border-white/10 bg-black/45 backdrop-blur-md">
            <div class="mx-auto flex h-20 w-full max-w-7xl items-center justify-between px-4 md:px-6 lg:px-12">
                <a href="{{ route('welcome') }}">
                    <x-athletegum-logo size="sm" text-color="white" />
                </a>

                <nav class="hidden items-center gap-6 bg-transparent md:flex lg:gap-8" style="background: transparent !important;">
                    <a href="{{ route('welcome') }}" class="bg-transparent px-2 py-2 text-sm font-semibold text-white/85 transition hover:text-white" style="background: transparent !important; color: rgba(255,255,255,0.9) !important; text-decoration: none !important;">Home</a>
                    <a href="{{ route('pages.how-it-works') }}" class="bg-transparent px-2 py-2 text-sm font-semibold text-white/85 transition hover:text-white" style="background: transparent !important; color: rgba(255,255,255,0.9) !important; text-decoration: none !important;">How It Works</a>
                    <a href="{{ route('pages.pricing') }}" class="bg-transparent px-2 py-2 text-sm font-semibold text-white/85 transition hover:text-white" style="background: transparent !important; color: rgba(255,255,255,0.9) !important; text-decoration: none !important;">Pricing</a>
                    <a href="{{ route('pages.about') }}" class="bg-transparent px-2 py-2 text-sm font-semibold text-white/85 transition hover:text-white" style="background: transparent !important; color: rgba(255,255,255,0.9) !important; text-decoration: none !important;">About</a>
                </nav>

                <div class="hidden items-center gap-3 lg:flex">
                    <a href="{{ route('login') }}" class="rounded-none border-2 border-white bg-transparent px-4 py-2 text-xs font-medium text-white transition hover:bg-white hover:text-black sm:px-5 sm:py-2.5 sm:text-sm">
                        Business Sign In
                    </a>
                    <a href="{{ route('athlete.login') }}" class="rounded-none border-2 border-white bg-transparent px-4 py-2 text-xs font-medium text-white transition hover:bg-white hover:text-black sm:px-5 sm:py-2.5 sm:text-sm">
                        Athlete Sign In
                    </a>
                </div>
            </div>
        </header>

        {{-- Hero --}}
        <section class="relative min-h-[86vh] overflow-hidden bg-black pb-24 pt-36 text-white lg:pb-32 lg:pt-44">
            <video
                class="absolute inset-0 h-full w-full scale-105 object-cover object-center grayscale brightness-75"
                autoplay
                muted
                loop
                playsinline
                preload="auto"
                poster="https://assets.mixkit.co/videos/32792/32792-thumb-720-0.jpg"
            >
                <source src="https://assets.mixkit.co/videos/32792/32792-720.mp4" type="video/mp4">
            </video>
            <div class="absolute inset-0 bg-black/55"></div>
            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(0,0,0,0.72)_0%,rgba(0,20,60,0.48)_42%,rgba(0,20,60,0.25)_100%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(0,0,0,0.2),transparent_55%)]"></div>

            <div class="relative z-10 mx-auto w-full max-w-7xl px-6 lg:px-12">
                <div class="max-w-3xl">
                    <h1 class="max-w-2xl text-5xl font-bold leading-[1.08] tracking-tight lg:text-7xl">
                        Pay Athletes for Real Work —
                        <span class="relative inline-block px-1">
                            <span class="inline-block">Safely and</span>
                            {{-- Underline bar effect --}}
                            <span class="pointer-events-none absolute bottom-[-6px] left-0 h-3 w-full bg-white"></span>
                        </span>
                        <span class="block lg:inline"> Transparently</span>
                    </h1>

                    <p class="mt-8 max-w-xl text-lg leading-relaxed text-white/80">
                        Create NIL deals, secure funds upfront, and only release payment after work is delivered and approved.
                    </p>

                    <div class="mt-12 flex flex-col gap-4 sm:flex-row">
                        <a href="{{ route('register') }}" class="inline-flex min-w-[220px] items-center justify-center gap-2 whitespace-nowrap rounded-none border-2 border-white bg-white px-8 py-3 text-sm font-semibold text-black transition duration-200 hover:translate-y-[-1px] hover:bg-zinc-100" style="text-decoration: none !important;">
                            Create Your First Deal
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                        <a href="{{ route('pages.how-it-works') }}" class="inline-flex min-w-[180px] items-center justify-center whitespace-nowrap rounded-none border-2 border-white/90 bg-white/5 px-8 py-3 text-sm font-semibold text-white backdrop-blur-sm transition duration-200 hover:translate-y-[-1px] hover:border-white hover:bg-white hover:text-black" style="text-decoration: none !important;">
                            Watch Demo
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Tagline strip --}}
        <section class="border-y border-white/10 bg-black py-16 text-white">
            <div class="mx-auto max-w-6xl px-6 lg:px-12">
                <p class="text-center text-2xl font-bold lg:text-4xl">
                    Built for modern NIL deals — trusted by businesses, athletes, and collectives.
                </p>
            </div>
        </section>

        {{-- How It Works --}}
        <section id="how-it-works" class="bg-black py-24 text-white">
            <div class="mx-auto max-w-6xl px-6 lg:px-12">
                <h2 class="mb-20 text-center text-4xl font-bold lg:text-6xl">How It Works</h2>

                <div class="grid gap-12 md:grid-cols-3">
                    {{-- Card 1 --}}
                    <article class="group">
                        <div class="relative mb-8">
                            <div class="absolute right-0 top-0 h-2 w-2 bg-white"></div>
                            <div class="grid h-32 w-32 place-items-center border-2 border-white transition group-hover:bg-white">
                                <div class="text-white transition group-hover:text-black">
                                    {{-- FileCheck --}}
                                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 14 1 1 3-3"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold">Create a Deal</h3>
                        <p class="mt-3 text-white/70">Set deliverables, timelines, and payment terms in minutes.</p>
                    </article>

                    {{-- Card 2 --}}
                    <article class="group">
                        <div class="relative mb-8">
                            <div class="absolute right-0 top-0 h-2 w-2 bg-white"></div>
                            <div class="grid h-32 w-32 place-items-center border-2 border-white transition group-hover:bg-white">
                                <div class="text-white transition group-hover:text-black">
                                    {{-- Eye --}}
                                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold">Track Progress</h3>
                        <p class="mt-3 text-white/70">Monitor every milestone with real-time updates and notifications.</p>
                    </article>

                    {{-- Card 3 --}}
                    <article class="group">
                        <div class="relative mb-8">
                            <div class="absolute right-0 top-0 h-2 w-2 bg-white"></div>
                            <div class="grid h-32 w-32 place-items-center border-2 border-white transition group-hover:bg-white">
                                <div class="text-white transition group-hover:text-black">
                                    {{-- CheckCircle2 --}}
                                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M22 4 12 14.01l-3-3"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold">Receive Pay</h3>
                        <p class="mt-3 text-white/70">Athletes get paid instantly when work is approved and verified.</p>
                    </article>
                </div>
            </div>
        </section>

        {{-- Image Break --}}
        <section class="relative h-[400px] overflow-hidden bg-black">
            <img
                src="https://images.unsplash.com/photo-1665114208033-150ffe629a1e?auto=format&fit=crop&w=1600&q=80"
                alt="Basketball court"
                class="h-full w-full object-cover grayscale"
            />
            <div class="absolute inset-0 bg-black/60"></div>
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center" style="color: #ffffff !important;">
                <div class="text-5xl font-bold text-white lg:text-7xl" style="color: #ffffff !important;">2,847</div>
                <div class="mt-3 text-xl font-medium uppercase tracking-[0.25em] text-white" style="color: #ffffff !important;">Active Deals</div>
            </div>
        </section>

        {{-- Built for Real NIL Work --}}
        <section class="bg-white py-24 text-black">
            <div class="mx-auto max-w-6xl px-6 lg:px-12">
                <h2 class="text-2xl font-bold lg:text-4xl">Built for Real NIL Work — Not Marketplaces</h2>

                <div class="mt-14 grid gap-12 md:grid-cols-3">
                    {{-- Feature 1 --}}
                    <div class="border-l-4 border-black pl-6">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center bg-black text-white">
                            {{-- Shield --}}
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2 4 5v6c0 5 3.5 9.74 8 11 4.5-1.26 8-6 8-11V5l-8-3Z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold">Escrow Protection</h3>
                        <p class="mt-3 text-black/70">Funds are locked until work is completed and approved.</p>
                    </div>

                    {{-- Feature 2 --}}
                    <div class="border-l-4 border-black pl-6">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center bg-black text-white">
                            {{-- Eye --}}
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold">Clear Deliverables</h3>
                        <p class="mt-3 text-black/70">Every deal includes precise deliverables and timelines.</p>
                    </div>

                    {{-- Feature 3 --}}
                    <div class="border-l-4 border-black pl-6">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center bg-black text-white">
                            {{-- CheckCircle2 --}}
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M22 4 12 14.01l-3-3"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold">Verified Payouts</h3>
                        <p class="mt-3 text-black/70">Payments trigger only when work is verified as done.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Choose Your Path --}}
        <section class="bg-black py-24 text-white">
            <div class="mx-auto max-w-6xl px-6 lg:px-12">
                <h2 class="text-center text-4xl font-bold lg:text-6xl">Choose Your Path</h2>

                <div class="mt-14 grid gap-8 lg:grid-cols-2">
                    {{-- Businesses --}}
                    <article class="group relative border-2 border-white p-10 transition hover:translate-y-[-2px] hover:bg-white hover:text-black">
                        <div class="absolute right-0 top-0 h-16 w-16 bg-white transition group-hover:bg-black"></div>

                        <div class="relative mb-6 flex items-center justify-start">
                            <div class="flex h-12 w-12 items-center justify-center border-2 border-white transition group-hover:border-black">
                                {{-- Building2 --}}
                                <svg class="h-6 w-6 text-white transition group-hover:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V9a2 2 0 0 1 2-2h4v14"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 21V5a2 2 0 0 1 2-2h4v18"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18"/>
                                </svg>
                            </div>
                        </div>

                        <h3 class="relative mb-4 text-3xl font-bold transition group-hover:text-black">For Businesses</h3>
                        <p class="relative mb-2 text-lg text-white/70 transition group-hover:text-black/70">
                            Plan campaigns, define deliverables, and manage every NIL agreement from one place.
                        </p>
                        <p class="relative mb-6 text-lg text-white/70 transition group-hover:text-black/70">
                            Keep deals funded, compliant, and easy to track for finance and legal.
                        </p>

                        <ul class="mb-8 space-y-2 text-sm transition group-hover:text-black">
                            <li class="flex items-center gap-3 text-white/90 transition group-hover:text-black/90">
                                <svg class="h-6 w-6 text-white transition group-hover:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 4 12 14.01l-3-3"/>
                                </svg>
                                Create and fund deals in minutes
                            </li>
                            <li class="flex items-center gap-3 text-white/90 transition group-hover:text-black/90">
                                <svg class="h-6 w-6 text-white transition group-hover:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 4 12 14.01l-3-3"/>
                                </svg>
                                Centralize approvals and revisions
                            </li>
                            <li class="flex items-center gap-3 text-white/90 transition group-hover:text-black/90">
                                <svg class="h-6 w-6 text-white transition group-hover:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 4 12 14.01l-3-3"/>
                                </svg>
                                Export-ready records for compliance
                            </li>
                        </ul>

                        <div class="relative">
                            <a href="{{ route('register') }}" class="block w-full rounded-none border-2 border-white bg-transparent px-6 py-3 text-center text-sm font-semibold text-white transition group-hover:border-black group-hover:bg-black group-hover:text-white">
                                Create Business Account
                            </a>
                        </div>
                    </article>

                    {{-- Athletes --}}
                    <article class="group relative border-2 border-white p-10 transition hover:translate-y-[-2px] hover:bg-white hover:text-black">
                        <div class="absolute right-0 top-0 h-16 w-16 bg-white transition group-hover:bg-black"></div>

                        <div class="relative mb-6 flex items-center justify-start">
                            <div class="flex h-12 w-12 items-center justify-center border-2 border-white transition group-hover:border-black">
                                {{-- Users --}}
                                <svg class="h-6 w-6 text-white transition group-hover:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/>
                                </svg>
                            </div>
                        </div>

                        <h3 class="relative mb-4 text-3xl font-bold transition group-hover:text-black">For Athletes</h3>
                        <p class="relative mb-2 text-lg text-white/70 transition group-hover:text-black/70">
                            Accept offers that clearly spell out what you owe and what you earn.
                        </p>
                        <p class="relative mb-6 text-lg text-white/70 transition group-hover:text-black/70">
                            Submit work, track payouts, and see every NIL deal in one dashboard.
                        </p>

                        <ul class="mb-8 space-y-2 text-sm transition group-hover:text-black">
                            <li class="flex items-center gap-3 text-white/90 transition group-hover:text-black/90">
                                <svg class="h-6 w-6 text-white transition group-hover:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 4 12 14.01l-3-3"/>
                                </svg>
                                No chasing payments or screenshots
                            </li>
                            <li class="flex items-center gap-3 text-white/90 transition group-hover:text-black/90">
                                <svg class="h-6 w-6 text-white transition group-hover:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 4 12 14.01l-3-3"/>
                                </svg>
                                Clear history of every payout
                            </li>
                            <li class="flex items-center gap-3 text-white/90 transition group-hover:text-black/90">
                                <svg class="h-6 w-6 text-white transition group-hover:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 4 12 14.01l-3-3"/>
                                </svg>
                                Verified partners and protected deals
                            </li>
                        </ul>

                        <div class="relative">
                            <a href="{{ route('athlete.register') }}" class="block w-full rounded-none border-2 border-white bg-transparent px-6 py-3 text-center text-sm font-semibold text-white transition group-hover:border-black group-hover:bg-black group-hover:text-white">
                                Create Athlete Profile
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        {{-- Everything You Need --}}
        <section class="bg-black py-24 text-white">
            <div class="mx-auto max-w-6xl px-6 lg:px-12">
                <h2 class="mb-12 text-center text-3xl font-bold lg:text-4xl">
                    Everything You Need to Run NIL Deals Properly
                </h2>
                <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                    @foreach ([
                        'Secure Escrow',
                        'Deal Templates',
                        'Progress Tracking',
                        'Compliance Tools',
                        'Analytics Dashboard',
                        'Auto Approvals',
                        'Team Management',
                        'Verified Profiles',
                    ] as $item)
                        <div class="flex aspect-square items-center justify-center border border-white/20 px-3 text-center text-sm font-semibold transition hover:bg-white hover:text-black">
                            {{ $item }}
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Trust Section --}}
        <section class="bg-white py-24 text-black">
            <div class="mx-auto max-w-6xl px-6 lg:px-12">
                <h2 class="mb-14 text-center text-3xl font-bold lg:text-4xl">
                    Built for Trust, Compliance, and Peace of Mind
                </h2>

                <div class="space-y-8">
                    <div class="flex items-start gap-4 border-l-4 border-black pl-6">
                        <svg class="mt-1 h-6 w-6 flex-shrink-0 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M22 4 12 14.01l-3-3"/>
                        </svg>
                        <p class="text-lg">Funds are held until work is approved.</p>
                    </div>

                    <div class="flex items-start gap-4 border-l-4 border-black pl-6">
                        <svg class="mt-1 h-6 w-6 flex-shrink-0 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M22 4 12 14.01l-3-3"/>
                        </svg>
                        <p class="text-lg">Payments are handled securely.</p>
                    </div>

                    <div class="flex items-start gap-4 border-l-4 border-black pl-6">
                        <svg class="mt-1 h-6 w-6 flex-shrink-0 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M22 4 12 14.01l-3-3"/>
                        </svg>
                        <p class="text-lg">Athletes control their own payouts.</p>
                    </div>

                    <div class="flex items-start gap-4 border-l-4 border-black pl-6">
                        <svg class="mt-1 h-6 w-6 flex-shrink-0 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M22 4 12 14.01l-3-3"/>
                        </svg>
                        <p class="text-lg">Businesses never pay for incomplete work.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Who It's For --}}
        <section class="bg-black py-24 text-white">
            <div class="mx-auto max-w-6xl px-6 lg:px-12">
                <h2 class="mb-16 text-center text-4xl font-bold lg:text-6xl">Who It's For</h2>

                <div class="grid gap-12 md:grid-cols-2">
                    <article class="border border-white/20 p-10">
                        <div class="mb-6 flex h-16 w-16 items-center justify-center bg-white text-black">
                            {{-- Building2 --}}
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V9a2 2 0 0 1 2-2h4v14"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 21V5a2 2 0 0 1 2-2h4v18"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18"/>
                            </svg>
                        </div>
                        <h3 class="text-3xl font-bold">For Businesses</h3>
                        <p class="mt-4 text-lg text-white/70">
                            Secure escrow, define deliverables, and release payouts only after approval.
                        </p>
                        <p class="mt-4 text-lg text-white/70">
                            Built for campaigns, collectives, and teams that need compliance-ready records.
                        </p>
                    </article>

                    <article class="border border-white/20 p-10">
                        <div class="mb-6 flex h-16 w-16 items-center justify-center bg-white text-black">
                            {{-- Users --}}
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/>
                            </svg>
                        </div>
                        <h3 class="text-3xl font-bold">For Athletes</h3>
                        <p class="mt-4 text-lg text-white/70">
                            Submit work, get verified, and receive payouts quickly—no chasing payments.
                        </p>
                        <p class="mt-4 text-lg text-white/70">
                            Transparent deal terms so you always know what’s expected and what you earn.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        {{-- Final CTA --}}
        <section class="relative h-[600px] overflow-hidden bg-black">
            <img
                src="https://images.unsplash.com/photo-1726195222148-fc8a7e7f37fa?auto=format&fit=crop&w=1600&q=80"
                alt="Athlete on track"
                class="absolute inset-0 z-0 h-full w-full object-cover object-center grayscale"
            />
            <div class="absolute inset-0 z-0 bg-black/70"></div>

            <div class="relative z-10 flex h-full items-center justify-center px-6 text-center lg:px-12">
                <div class="mx-auto max-w-4xl">
                    <h2 class="text-5xl font-bold text-white lg:text-7xl">Start Your First Deal in Minutes</h2>
                    <p class="mt-6 text-xl text-white/80">
                        Create a deal, fund it securely, and pay only when the work is done.
                    </p>

                    <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-none bg-white px-8 py-3 text-sm font-semibold text-black transition hover:bg-white">
                            Create Business Account
                        </a>
                        <a href="{{ route('athlete.register') }}" class="inline-flex items-center justify-center rounded-none border-2 border-white bg-transparent px-8 py-3 text-sm font-semibold text-white transition hover:bg-white hover:text-black">
                            Create Athlete Profile
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="border-t border-white/10 bg-black py-12 text-white">
            <div class="mx-auto flex w-full max-w-7xl flex-col items-start justify-between gap-6 px-6 md:flex-row lg:px-12">
                <p class="text-xs text-white/60">&copy; {{ date('Y') }} AthleteGum. All rights reserved.</p>
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <a href="{{ route('pages.about') }}" class="text-white/40 transition hover:text-white">About</a>
                    <a href="{{ route('pages.privacy') }}" class="text-white/40 transition hover:text-white">Privacy</a>
                    <a href="{{ route('pages.terms') }}" class="text-white/40 transition hover:text-white">Terms</a>
                    <a href="{{ route('pages.contact') }}" class="text-white/40 transition hover:text-white">Contact</a>
                </div>
            </div>
        </footer>
    </body>
</html>

