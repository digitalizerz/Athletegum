<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>How It Works - AthleteGum</title>

        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23111'/><text x='50' y='65' font-size='50' font-weight='bold' fill='white' text-anchor='middle'>A</text></svg>" />
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-black font-sans antialiased text-white">
        <header class="sticky top-0 z-50 w-full border-b border-white/10 bg-black/90 backdrop-blur">
            <div class="mx-auto flex h-20 w-full max-w-7xl items-center justify-between px-4 md:px-6 lg:px-12">
                <a href="{{ route('welcome') }}">
                    <x-athletegum-logo size="sm" text-color="white" />
                </a>

                <nav class="hidden items-center gap-6 bg-transparent md:flex lg:gap-8" style="background: transparent !important;">
                    <a href="{{ route('welcome') }}" class="bg-transparent px-2 py-2 text-sm font-semibold text-white/80 transition hover:text-white" style="background: transparent !important; color: rgba(255,255,255,0.9) !important; text-decoration: none !important;">Home</a>
                    <a href="{{ route('pages.how-it-works') }}" class="bg-transparent px-2 py-2 text-sm font-semibold text-white" style="background: transparent !important; color: #ffffff !important; text-decoration: none !important;">How It Works</a>
                    <a href="{{ route('pages.pricing') }}" class="bg-transparent px-2 py-2 text-sm font-semibold text-white/80 transition hover:text-white" style="background: transparent !important; color: rgba(255,255,255,0.9) !important; text-decoration: none !important;">Pricing</a>
                    <a href="{{ route('pages.about') }}" class="bg-transparent px-2 py-2 text-sm font-semibold text-white/80 transition hover:text-white" style="background: transparent !important; color: rgba(255,255,255,0.9) !important; text-decoration: none !important;">About</a>
                </nav>

                <div class="hidden items-center gap-3 lg:flex">
                    <a href="{{ route('login') }}" class="rounded-none border-2 border-white px-4 py-2 text-xs font-medium text-white transition hover:bg-white hover:text-black sm:px-5 sm:py-2.5 sm:text-sm">Business Sign In</a>
                    <a href="{{ route('athlete.login') }}" class="rounded-none border-2 border-white px-4 py-2 text-xs font-medium text-white transition hover:bg-white hover:text-black sm:px-5 sm:py-2.5 sm:text-sm">Athlete Sign In</a>
                </div>
            </div>
        </header>

        <main>
            <section class="border-b border-white/10 bg-black py-24">
                <div class="mx-auto max-w-4xl px-6 text-center lg:px-12">
                    <h1 class="text-6xl font-bold leading-tight tracking-tight text-white lg:text-8xl">
                        How AthleteGum
                        <span class="relative inline-block px-1">
                            Works
                            <span class="pointer-events-none absolute bottom-2 left-0 h-4 w-full bg-white"></span>
                        </span>
                    </h1>
                    <p class="mx-auto mt-8 max-w-2xl text-xl text-white/80">
                        A clear, secure system for launching NIL deals, reviewing submissions, and releasing payment with confidence.
                    </p>
                </div>
            </section>

            <section class="bg-black py-24">
                <div class="mx-auto max-w-6xl px-6 lg:px-12">
                    <div class="mb-12 flex items-center gap-4">
                        <div class="grid h-16 w-16 place-items-center bg-white text-black">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V9a2 2 0 0 1 2-2h4v14"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 21V5a2 2 0 0 1 2-2h4v18"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18"/>
                            </svg>
                        </div>
                        <h2 class="text-5xl font-bold text-white">For Businesses</h2>
                    </div>

                    <div class="space-y-8">
                        <article class="relative border-l-4 border-white py-6 pl-12 transition hover:border-white/50">
                            <div class="absolute left-0 top-10 grid h-12 w-12 -translate-x-1/2 place-items-center rounded-full border-4 border-white bg-black text-xl font-bold transition hover:bg-white hover:text-black">01</div>
                            <h3 class="mb-2 text-3xl font-bold text-white">Create a Deal</h3>
                            <p class="mb-2 text-lg text-white/80">Create a deal with athletes.</p>
                            <p class="text-white/70">Define what you need, set clear deliverables, and establish payment terms upfront.</p>
                        </article>

                        <article class="relative border-l-4 border-white py-6 pl-12 transition hover:border-white/50">
                            <div class="absolute left-0 top-10 grid h-12 w-12 -translate-x-1/2 place-items-center rounded-full border-4 border-white bg-black text-xl font-bold transition hover:bg-white hover:text-black">02</div>
                            <h3 class="mb-2 text-3xl font-bold text-white">Assign Deliverables</h3>
                            <p class="mb-2 text-lg text-white/80">Set specific tasks you want the athlete to complete.</p>
                            <p class="text-white/70">Upload brand guidelines, content requirements, and deadlines. Athletes see exactly what&apos;s expected.</p>
                        </article>

                        <article class="relative border-l-4 border-white py-6 pl-12 transition hover:border-white/50">
                            <div class="absolute left-0 top-10 grid h-12 w-12 -translate-x-1/2 place-items-center rounded-full border-4 border-white bg-black text-xl font-bold transition hover:bg-white hover:text-black">03</div>
                            <h3 class="mb-2 text-3xl font-bold text-white">Review &amp; Approve</h3>
                            <p class="mb-2 text-lg text-white/80">Athletes upload their work for you to review.</p>
                            <p class="text-white/70">Request revisions if needed. Approve deliverables when they meet your standards.</p>
                        </article>

                        <article class="relative border-l-4 border-white py-6 pl-12 transition hover:border-white/50">
                            <div class="absolute left-0 top-10 grid h-12 w-12 -translate-x-1/2 place-items-center rounded-full border-4 border-white bg-black text-xl font-bold transition hover:bg-white hover:text-black">04</div>
                            <h3 class="mb-2 text-3xl font-bold text-white">Payment is Released</h3>
                            <p class="mb-2 text-lg text-white/80">Funds are transferred to athletes once work is approved.</p>
                            <p class="text-white/70">Automatic, secure, and instant. Athletes get paid within hours of approval.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="relative my-12 h-[400px] overflow-hidden">
                <img src="https://images.unsplash.com/photo-1745847768380-2caeadbb3b71?auto=format&fit=crop&w=1600&q=80" alt="Business handshake" class="h-full w-full object-cover grayscale" />
                <div class="absolute inset-0 bg-black/70"></div>
                <div class="absolute inset-0 grid place-items-center text-center text-white" style="color: #ffffff !important;">
                    <div>
                        <p class="text-6xl font-bold text-white" style="color: #ffffff !important;">TRANSPARENT</p>
                        <p class="mt-4 text-xl uppercase tracking-[0.3em] text-white/90" style="color: rgba(255,255,255,0.9) !important;">Every Step. Every Time.</p>
                    </div>
                </div>
            </section>

            <section class="bg-white py-24 text-black">
                <div class="mx-auto max-w-6xl px-6 lg:px-12">
                    <div class="mb-12 flex items-center gap-4">
                        <div class="grid h-16 w-16 place-items-center bg-black text-white">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/>
                            </svg>
                        </div>
                        <h2 class="text-5xl font-bold">For Athletes</h2>
                    </div>

                    <div class="space-y-8">
                        <article class="relative border-l-4 border-black py-6 pl-12 transition hover:border-black/50">
                            <div class="absolute left-0 top-10 grid h-12 w-12 -translate-x-1/2 place-items-center rounded-full border-4 border-black bg-white text-xl font-bold transition hover:bg-black hover:text-white">01</div>
                            <h3 class="mb-2 text-3xl font-bold">Join for Free</h3>
                            <p class="mb-2 text-lg text-black/80">Create your athlete profile at no cost.</p>
                            <p class="text-black/50">Add your sport, stats, social following, and what types of deals you&apos;re interested in.</p>
                        </article>

                        <article class="relative border-l-4 border-black py-6 pl-12 transition hover:border-black/50">
                            <div class="absolute left-0 top-10 grid h-12 w-12 -translate-x-1/2 place-items-center rounded-full border-4 border-black bg-white text-xl font-bold transition hover:bg-black hover:text-white">02</div>
                            <h3 class="mb-2 text-3xl font-bold">Accept a Deal</h3>
                            <p class="mb-2 text-lg text-black/80">Review the offer and accept if it fits your brand.</p>
                            <p class="text-black/50">See exactly what&apos;s expected, payment amount, and timeline before committing.</p>
                        </article>

                        <article class="relative border-l-4 border-black py-6 pl-12 transition hover:border-black/50">
                            <div class="absolute left-0 top-10 grid h-12 w-12 -translate-x-1/2 place-items-center rounded-full border-4 border-black bg-white text-xl font-bold transition hover:bg-black hover:text-white">03</div>
                            <h3 class="mb-2 text-3xl font-bold">Deliver the Work</h3>
                            <p class="mb-2 text-lg text-black/80">Complete the deliverables as outlined in the deal.</p>
                            <p class="text-black/50">Upload your content directly to the platform for review. Request feedback if needed.</p>
                        </article>

                        <article class="relative border-l-4 border-black py-6 pl-12 transition hover:border-black/50">
                            <div class="absolute left-0 top-10 grid h-12 w-12 -translate-x-1/2 place-items-center rounded-full border-4 border-black bg-white text-xl font-bold transition hover:bg-black hover:text-white">04</div>
                            <h3 class="mb-2 text-3xl font-bold">Get Paid</h3>
                            <p class="mb-2 text-lg text-black/80">Receive payment instantly once your work is approved.</p>
                            <p class="text-black/50">Funds hit your account within hours. Track all earnings through Stripe.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="border-t border-white/10 bg-black py-24">
                <div class="mx-auto max-w-6xl px-6 lg:px-12">
                    <h2 class="mb-16 text-center text-5xl font-bold text-white">Secure Payments, Built for Trust</h2>
                    <div class="grid gap-12 md:grid-cols-2">
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <svg class="mt-1 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path stroke-linecap="round" stroke-linejoin="round" d="M22 4 12 14.01l-3-3"/></svg>
                                <p class="text-lg text-white/80">Payments are held in escrow until work is completed</p>
                            </div>
                            <div class="flex items-start gap-4">
                                <svg class="mt-1 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path stroke-linecap="round" stroke-linejoin="round" d="M22 4 12 14.01l-3-3"/></svg>
                                <p class="text-lg text-white/80">Businesses can&apos;t pull funds after an athlete has started work</p>
                            </div>
                            <div class="flex items-start gap-4">
                                <svg class="mt-1 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path stroke-linecap="round" stroke-linejoin="round" d="M22 4 12 14.01l-3-3"/></svg>
                                <p class="text-lg text-white/80">Athletes can&apos;t access funds until businesses approve deliverables</p>
                            </div>
                            <div class="flex items-start gap-4">
                                <svg class="mt-1 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path stroke-linecap="round" stroke-linejoin="round" d="M22 4 12 14.01l-3-3"/></svg>
                                <p class="text-lg text-white/80">Disputes are resolved fairly through our mediation process</p>
                            </div>
                        </div>
                        <div class="border-2 border-white p-8">
                            <div class="mb-6 grid h-16 w-16 place-items-center bg-white text-black">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2 4 5v6c0 5 3.5 9.74 8 11 4.5-1.26 8-6 8-11V5l-8-3Z"/></svg>
                            </div>
                            <h3 class="mb-4 text-2xl font-bold text-white">Escrow Protection</h3>
                            <p class="leading-relaxed text-white/70">Every transaction is protected by structured escrow logic so both parties stay aligned from kickoff to payout.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-white py-24 text-black">
                <div class="mx-auto max-w-4xl px-6 lg:px-12">
                    <h2 class="mb-4 text-5xl font-bold">Revisions &amp; Disputes</h2>
                    <div class="space-y-8">
                        <article class="border-l-4 border-black py-2 pl-6">
                            <h3 class="mb-3 text-2xl font-bold">What if revisions are needed?</h3>
                            <p class="text-lg leading-relaxed text-black/70">Businesses can request revisions through the platform. Athletes receive detailed feedback on what needs to be changed. Most deals include 1-2 rounds of revisions in the original agreement.</p>
                        </article>
                        <article class="border-l-4 border-black py-2 pl-6">
                            <h3 class="mb-3 text-2xl font-bold">What if a deal isn&apos;t approved?</h3>
                            <p class="text-lg leading-relaxed text-black/70">If a business rejects work without valid reason, athletes can file a dispute. Our mediation team reviews the deliverables against the original agreement and makes a fair decision.</p>
                        </article>
                        <article class="border-l-4 border-black py-2 pl-6">
                            <h3 class="mb-3 text-2xl font-bold">How long do disputes take?</h3>
                            <p class="text-lg leading-relaxed text-black/70">Most disputes are resolved within 3-5 business days. Our team reviews all submitted work, communications, and the original deal terms to reach a fair resolution.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="border-t border-white/10 bg-black py-24">
                <div class="mx-auto max-w-4xl px-6 lg:px-12">
                    <h2 class="mb-4 text-5xl font-bold text-white">When does AthleteGum charge fees?</h2>
                    <div class="mt-10 grid gap-8 md:grid-cols-2">
                        <article class="group border-2 border-white p-8 transition hover:bg-white hover:text-black">
                            <div class="mb-4 grid h-12 w-12 place-items-center bg-white text-black transition group-hover:bg-black group-hover:text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21V9a2 2 0 0 1 2-2h4v14"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 21V5a2 2 0 0 1 2-2h4v18"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18"/></svg>
                            </div>
                            <h3 class="mb-4 text-2xl font-bold text-white transition group-hover:text-black">For Businesses</h3>
                            <p class="text-white/80 transition group-hover:text-black/70">AthleteGum takes a percentage fee when a business creates a completed deal. This covers platform costs, escrow management, and customer support.</p>
                        </article>

                        <article class="group border-2 border-white p-8 transition hover:bg-white hover:text-black">
                            <div class="mb-4 grid h-12 w-12 place-items-center bg-white text-black transition group-hover:bg-black group-hover:text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 0 1 0 7.75"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/></svg>
                            </div>
                            <h3 class="mb-4 text-2xl font-bold text-white transition group-hover:text-black">For Athletes</h3>
                            <p class="text-white/80 transition group-hover:text-black/70">Athletes pay nothing. AthleteGum is completely free for athletes to join, browse deals, and get paid. We only charge businesses who post opportunities.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="border-t border-white/10 bg-black py-32">
                <div class="mx-auto max-w-4xl px-6 text-center lg:px-12">
                    <h2 class="text-6xl font-bold leading-tight text-white">Start Your First Deal with Confidence</h2>
                    <p class="mx-auto mb-12 mt-6 max-w-2xl text-xl text-white/80">Create a deal, secure funding, and pay only when approved work is delivered.</p>
                    <div class="flex flex-col justify-center gap-4 sm:flex-row">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-none bg-white px-12 py-5 text-sm font-bold text-black transition hover:bg-zinc-100">Create Your First Deal</a>
                        <a href="{{ route('pages.contact') }}" class="inline-flex items-center justify-center rounded-none border-2 border-white px-12 py-5 text-sm font-bold text-white transition hover:bg-white hover:text-black">Contact Sales</a>
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
                <p class="mt-8 border-t border-white/10 pt-8 text-center text-xs text-white/75">&copy; {{ date('Y') }} AthleteGum. All rights reserved.</p>
            </div>
        </footer>
    </body>
</html>
