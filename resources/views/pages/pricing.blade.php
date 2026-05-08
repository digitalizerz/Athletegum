<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Pricing - AthleteGum</title>

        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23111'/><text x='50' y='65' font-size='50' font-weight='bold' fill='white' text-anchor='middle'>A</text></svg>" />
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-black font-sans antialiased text-white">
        <header class="sticky top-0 z-50 w-full border-b border-white/10 bg-black/90 backdrop-blur text-white">
            <div class="mx-auto flex h-20 w-full max-w-7xl items-center justify-between px-4 md:px-6 lg:px-12">
                <a href="{{ route('welcome') }}">
                    <x-athletegum-logo size="sm" text-color="white" />
                </a>

                <nav class="hidden items-center gap-6 md:flex lg:gap-8">
                    <a href="{{ route('welcome') }}" class="px-2 py-2 text-sm font-semibold text-white/80 transition hover:text-white" style="background: transparent !important; color: rgba(255,255,255,0.8) !important;">Home</a>
                    <a href="{{ route('pages.how-it-works') }}" class="px-2 py-2 text-sm font-semibold text-white/80 transition hover:text-white" style="background: transparent !important; color: rgba(255,255,255,0.8) !important;">How It Works</a>
                    <a href="{{ route('pages.pricing') }}" class="px-2 py-2 text-sm font-semibold text-white" style="background: transparent !important; color: #ffffff !important;">Pricing</a>
                    <a href="{{ route('pages.about') }}" class="px-2 py-2 text-sm font-semibold text-white/80 transition hover:text-white" style="background: transparent !important; color: rgba(255,255,255,0.8) !important;">About</a>
                </nav>

                <div class="hidden items-center gap-3 lg:flex">
                    <a href="{{ route('login') }}" class="rounded-none border-2 border-white px-4 py-2 text-xs font-medium text-white transition hover:bg-white hover:text-black sm:px-5 sm:py-2.5 sm:text-sm">Business Sign In</a>
                    <a href="{{ route('athlete.login') }}" class="rounded-none border-2 border-white px-4 py-2 text-xs font-medium text-white transition hover:bg-white hover:text-black sm:px-5 sm:py-2.5 sm:text-sm">Athlete Sign In</a>
                </div>
            </div>
        </header>

        <main>
            <section class="relative min-h-[78vh] overflow-hidden border-b border-white/10 bg-black pb-44 pt-20 text-white lg:min-h-[82vh] lg:pb-56 lg:pt-24">
                <video
                    class="absolute inset-0 h-full w-full scale-105 object-cover object-center grayscale brightness-75"
                    autoplay
                    muted
                    loop
                    playsinline
                    preload="auto"
                    poster="https://assets.mixkit.co/videos/22977/22977-thumb-720-0.jpg"
                >
                    {{-- Primary: Mixkit “Business handshake close up” (free 720p). Fallback: local asset if CDN unavailable. --}}
                    <source src="https://assets.mixkit.co/videos/22977/22977-720.mp4" type="video/mp4">
                    <source src="{{ asset('videos/hero-track.mp4') }}" type="video/mp4">
                </video>
                <div class="absolute inset-0 bg-black/60"></div>
                <div class="absolute inset-0 bg-[linear-gradient(120deg,rgba(0,0,0,0.78)_0%,rgba(15,15,15,0.5)_45%,rgba(0,0,0,0.35)_100%)]"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_30%,rgba(255,255,255,0.06),transparent_50%)]"></div>

                <div class="relative z-10 mx-auto w-full max-w-7xl px-6 lg:px-12">
                    <div class="mx-auto max-w-4xl text-center">
                        <h1 class="text-6xl font-bold leading-tight tracking-tight text-white lg:text-7xl" style="color: #ffffff !important;">
                            Simple, Fair<br>
                            <span class="relative inline-block px-1">
                                Pricing
                                <span class="pointer-events-none absolute -bottom-1.5 left-0 h-2.5 w-full bg-white"></span>
                            </span>
                            for NIL<br>Deals
                        </h1>
                        <p class="mx-auto mt-6 max-w-2xl text-white/80">
                            Athletes use AthleteGum for free. Businesses pick the plan that matches deal volume and workflow complexity.
                        </p>
                    </div>
                </div>
            </section>

            <section class="bg-black py-16 text-white">
                <div class="mx-auto max-w-6xl px-6 lg:px-12">
                    <div class="mb-6 flex items-center gap-3">
                        <div class="grid h-6 w-6 place-items-center border border-white text-[10px] font-bold">A</div>
                        <h2 class="text-3xl font-bold text-white" style="color: #ffffff !important;">For Athletes &mdash; Always Free</h2>
                    </div>

                    <div class="border-2 border-white p-8 md:max-w-3xl">
                        <h3 class="text-5xl font-bold text-white" style="color: #ffffff !important;">Free</h3>
                        <p class="mt-2 text-white/70">Athletes never pay to join or complete deals on AthleteGum.</p>
                        <ul class="mt-8 space-y-3 text-white/80">
                            <li>&#10003; Create and manage your athlete profile</li>
                            <li>&#10003; Accept deals and review terms upfront</li>
                            <li>&#10003; Submit deliverables and handle revisions</li>
                            <li>&#10003; Get paid securely through Stripe</li>
                            <li>&#10003; Track payout history and earnings</li>
                        </ul>
                        <div class="mt-8 border-t border-white/20 pt-6">
                            <p class="text-white/65">Athletes keep 100% of their payout. No hidden subscriptions, ever.</p>
                        </div>
                        <a href="{{ route('athlete.register') }}" class="mt-8 inline-flex w-full items-center justify-center rounded-none bg-white px-8 py-3 text-sm font-semibold text-black transition hover:bg-zinc-100">Create Athlete Profile</a>
                    </div>
                </div>
            </section>

            <section class="bg-white py-16 text-black">
                <div class="mx-auto max-w-6xl px-6 lg:px-12">
                    <div class="mb-10 flex items-center gap-3">
                        <div class="grid h-6 w-6 place-items-center border border-black text-[10px] font-bold">A</div>
                        <h2 class="text-3xl font-bold">For Businesses &mdash; Plans</h2>
                    </div>
                    <div class="grid gap-6 lg:grid-cols-3">
                        <article class="relative border-2 border-black p-6">
                            <div class="absolute right-0 top-0 h-6 w-6 bg-black"></div>
                            <h3 class="text-4xl font-bold">$0</h3>
                            <p class="mt-2 text-black/70">Starter plan for first-time campaigns.</p>
                            <ul class="mt-6 space-y-2 text-sm text-black/80">
                                <li>&#10003; Up to 3 active deals</li><li>&#10003; Escrow protection</li><li>&#10003; Deliverable approval flow</li><li>&#10003; Basic reporting</li>
                            </ul>
                            <p class="mt-6 border-t border-black/20 pt-4 text-sm font-semibold">10% platform fee per completed deal</p>
                            <a href="{{ route('register') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-none border-2 border-black px-4 py-2 text-xs font-semibold text-black transition hover:bg-black hover:text-white">Get Started Free</a>
                        </article>
                        <article class="relative border-2 border-black bg-black p-6 text-white">
                            <div class="absolute right-0 top-0 h-6 w-6 bg-white"></div>
                            <h3 class="text-4xl font-bold">$49</h3>
                            <p class="mt-2 text-white/75">Best for brands running ongoing NIL deals.</p>
                            <ul class="mt-6 space-y-2 text-sm text-white/85">
                                <li>&#10003; Unlimited deals</li><li>&#10003; Lower platform fee</li><li>&#10003; Advanced deal tracking</li><li>&#10003; Receipts and exports</li>
                            </ul>
                            <p class="mt-6 border-t border-white/20 pt-4 text-sm font-semibold">8% platform fee per completed deal</p>
                            <a href="{{ route('subscriptions.checkout', 'pro') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-none bg-white px-4 py-2 text-xs font-semibold text-black transition hover:bg-zinc-100">Upgrade to Pro</a>
                        </article>
                        <article class="relative border-2 border-black p-6">
                            <div class="absolute right-0 top-0 h-6 w-6 bg-black"></div>
                            <h3 class="text-4xl font-bold">$99</h3>
                            <p class="mt-2 text-black/70">For agencies, collectives, and high-volume teams.</p>
                            <ul class="mt-6 space-y-2 text-sm text-black/80">
                                <li>&#10003; Lowest fee tier</li><li>&#10003; Team and role management</li><li>&#10003; Deal templates</li><li>&#10003; Priority support</li>
                            </ul>
                            <p class="mt-6 border-t border-black/20 pt-4 text-sm font-semibold">6% platform fee per completed deal</p>
                            <a href="{{ route('subscriptions.checkout', 'growth') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-none border-2 border-black px-4 py-2 text-xs font-semibold text-black transition hover:bg-black hover:text-white">Upgrade to Growth</a>
                        </article>
                    </div>
                </div>
            </section>

            <section class="border-t border-white/10 bg-black py-16 text-white">
                <div class="mx-auto max-w-6xl px-6 lg:px-12">
                    <h2 class="mb-10 text-center text-4xl font-bold text-white" style="color: #ffffff !important;">No surprises. No lock-in.</h2>
                    <div class="grid gap-8 md:grid-cols-2">
                        <div class="border-l-4 border-white pl-5"><h3 class="text-xl font-bold text-white" style="color: #ffffff !important;">Completely upgrade-friendly</h3><p class="mt-2 text-white/75">Scale your plan up or down as campaign volume changes.</p></div>
                        <div class="border-l-4 border-white pl-5"><h3 class="text-xl font-bold text-white" style="color: #ffffff !important;">Fees only on completed work</h3><p class="mt-2 text-white/75">No payout release means no completion fee charged.</p></div>
                    </div>
                </div>
            </section>

            <section class="bg-white py-16 text-black">
                <div class="mx-auto max-w-5xl px-6 lg:px-12">
                    <h2 class="mb-10 text-center text-4xl font-bold">FAQ</h2>
                    <div class="space-y-6">
                        <article class="border-l-4 border-black pl-5"><h3 class="text-xl font-bold">Do athletes ever pay?</h3><p class="mt-2 text-black/70">No. Athletes use AthleteGum completely free.</p></article>
                        <article class="border-l-4 border-black pl-5"><h3 class="text-xl font-bold">Can I switch plans anytime?</h3><p class="mt-2 text-black/70">Yes. Upgrade or downgrade whenever your volume changes.</p></article>
                        <article class="border-l-4 border-black pl-5"><h3 class="text-xl font-bold">What happens if work is not approved?</h3><p class="mt-2 text-black/70">Funds are not released and no completion fee is charged.</p></article>
                        <article class="border-l-4 border-black pl-5"><h3 class="text-xl font-bold">Will enterprise plans be available?</h3><p class="mt-2 text-black/70">Yes. Custom enterprise options are planned for larger organizations.</p></article>
                    </div>
                </div>
            </section>

            <section class="border-t border-white/10 bg-black py-24 text-white">
                <div class="mx-auto max-w-4xl px-6 text-center lg:px-12">
                    <h2 class="text-5xl font-bold leading-tight text-white" style="color: #ffffff !important;">Start Your First Deal<br>with Confidence</h2>
                    <p class="mx-auto mb-10 mt-4 max-w-2xl text-white/75">Create a deal, fund it securely, and pay only when the work is done.</p>
                    <div class="flex flex-col justify-center gap-4 sm:flex-row">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-none bg-white px-10 py-4 text-sm font-bold text-black transition hover:bg-zinc-100">Create Business Account</a>
                        <a href="{{ route('athlete.register') }}" class="inline-flex items-center justify-center rounded-none border-2 border-white px-10 py-4 text-sm font-bold text-white transition hover:bg-white hover:text-black">Create Athlete Profile</a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-white/10 bg-black py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center justify-between gap-6 md:flex-row">
                    <x-athletegum-logo size="sm" text-color="white" />
                    <div class="flex flex-wrap items-center justify-center gap-4 text-sm">
                        <a href="{{ route('pages.about') }}" class="text-white/80 transition hover:text-white">About</a>
                        <a href="{{ route('pages.privacy') }}" class="text-white/80 transition hover:text-white">Privacy</a>
                        <a href="{{ route('pages.terms') }}" class="text-white/80 transition hover:text-white">Terms</a>
                        <a href="{{ route('pages.contact') }}" class="text-white/80 transition hover:text-white">Contact</a>
                    </div>
                </div>
                <p class="mt-8 border-t border-white/10 pt-8 text-center text-xs text-white/70">&copy; {{ date('Y') }} AthleteGum. All rights reserved.</p>
            </div>
        </footer>
    </body>
</html>
