<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>About - AthleteGum</title>

        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23111'/><text x='50' y='65' font-size='50' font-weight='bold' fill='white' text-anchor='middle'>A</text></svg>" />
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .about-reveal-hero-l {
                opacity: 0;
                transform: translateX(-50px);
                transition: opacity 0.8s ease, transform 0.8s ease;
            }
            .about-reveal-hero-r {
                opacity: 0;
                transform: translateX(50px);
                transition: opacity 0.8s ease, transform 0.8s ease;
            }
            .about-reveal-hero-l.about-visible,
            .about-reveal-hero-r.about-visible {
                opacity: 1;
                transform: translateX(0);
            }
            .about-reveal-up {
                opacity: 0;
                transform: translateY(28px);
                transition: opacity 0.65s ease, transform 0.65s ease;
                transition-delay: var(--about-delay, 0s);
            }
            .about-reveal-up.about-visible {
                opacity: 1;
                transform: translateY(0);
            }
            .about-reveal-x {
                opacity: 0;
                transform: translateX(-20px);
                transition: opacity 0.55s ease, transform 0.55s ease;
                transition-delay: var(--about-delay, 0s);
            }
            .about-reveal-x.about-visible {
                opacity: 1;
                transform: translateX(0);
            }
            .about-reveal-l30 {
                opacity: 0;
                transform: translateX(-30px);
                transition: opacity 0.7s ease, transform 0.7s ease;
            }
            .about-reveal-r30 {
                opacity: 0;
                transform: translateX(30px);
                transition: opacity 0.7s ease, transform 0.7s ease;
            }
            .about-reveal-l30.about-visible,
            .about-reveal-r30.about-visible {
                opacity: 1;
                transform: translateX(0);
            }
        </style>
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
                    <a href="{{ route('pages.pricing') }}" class="px-2 py-2 text-sm font-semibold text-white/80 transition hover:text-white" style="background: transparent !important; color: rgba(255,255,255,0.8) !important;">Pricing</a>
                    <a href="{{ route('pages.about') }}" class="px-2 py-2 text-sm font-semibold text-white" style="background: transparent !important; color: #ffffff !important;">About</a>
                </nav>

                <div class="hidden items-center gap-3 lg:flex">
                    <a href="{{ route('login') }}" class="rounded-none border-2 border-white px-4 py-2 text-xs font-medium text-white transition hover:bg-white hover:text-black sm:px-5 sm:py-2.5 sm:text-sm">Business Sign In</a>
                    <a href="{{ route('athlete.login') }}" class="rounded-none border-2 border-white px-4 py-2 text-xs font-medium text-white transition hover:bg-white hover:text-black sm:px-5 sm:py-2.5 sm:text-sm">Athlete Sign In</a>
                </div>
            </div>
        </header>

        <main>
            {{-- Hero --}}
            <section class="grid border-b border-white/10 bg-black py-16 text-white lg:grid-cols-2 lg:py-0">
                <div class="about-reveal-hero-l flex flex-col justify-center px-6 py-20 lg:px-12 lg:py-44" data-about-hero="left" style="color: #ffffff !important;">
                    <h1 class="text-6xl font-bold uppercase leading-[1.1] text-white lg:text-8xl" style="color: #ffffff !important;">
                        We built this for
                        <span class="mt-2 inline-block bg-white px-3 py-2 text-black lg:px-5 lg:py-3" style="color: #000000 !important;">you</span>
                    </h1>
                    <p class="mt-8 max-w-lg text-xl text-white/80" style="color: rgba(255,255,255,0.8) !important;">
                        AthleteGum helps businesses pay athletes for real work &mdash; safely and transparently.
                    </p>
                </div>
                <div class="about-reveal-hero-r relative min-h-[320px] lg:min-h-[600px]" data-about-hero="right">
                    <img
                        src="https://images.unsplash.com/photo-1773949122578-8886f0ee48da?auto=format&amp;fit=crop&amp;w=1600&amp;q=80"
                        alt=""
                        class="h-full w-full object-cover object-center grayscale"
                        loading="eager"
                        decoding="async"
                    >
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-black via-black/50 to-transparent"></div>
                </div>
            </section>

            {{-- Mission --}}
            <section class="border-b border-white/10 bg-black py-28 text-white lg:py-36">
                <div class="mx-auto max-w-5xl px-6 py-12 sm:px-8 lg:px-12 lg:py-16">
                    <p class="mb-16 text-3xl font-bold leading-tight lg:mb-20 lg:text-5xl">
                        <span class="text-white" style="color: #ffffff !important;">We&apos;re fixing the broken NIL deals that hurt both athletes and businesses.</span>
                        <span class="text-white/40" style="color: rgba(255,255,255,0.4) !important;"><br>Online deliverables, local brand deals, and easy pay for both sides.</span>
                    </p>
                    <p class="max-w-3xl text-xl leading-relaxed text-white/60" style="color: rgba(255,255,255,0.6) !important;">
                        We built AthleteGum so expectations are clear, money is protected, and both sides can move fast without the usual NIL headaches.
                    </p>
                </div>
            </section>

            {{-- Why we exist --}}
            <section class="bg-white py-28 text-black lg:py-36">
                <div class="mx-auto max-w-6xl px-6 py-12 sm:px-8 lg:px-12 lg:py-16">
                    <h2 class="text-5xl font-bold lg:text-7xl">Why AthleteGum Exists</h2>
                    <p class="mt-10 max-w-2xl text-2xl text-black/60">
                        Paying athletes shouldn&apos;t be risky, vague, or messy.
                    </p>

                    <p class="mt-20 text-xl font-semibold text-black/80 lg:mt-24">Many businesses hesitate to work with athletes because of:</p>
                    <div class="mt-6 grid gap-6 md:grid-cols-2">
                        @php
                            $problems = [
                                'Unclear expectations',
                                'Unverified deliverables',
                                'Payment disputes',
                                'Trust issues on both sides',
                            ];
                        @endphp
                        @foreach ($problems as $i => $label)
                            <div
                                class="about-reveal-x flex items-center gap-4 border-2 border-black p-6"
                                data-about-reveal
                                style="--about-delay: {{ $i * 0.05 }}s"
                            >
                                <svg class="h-8 w-8 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12" />
                                </svg>
                                <span class="text-xl font-semibold">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-20 border-l-4 border-black py-4 pl-8 lg:mt-24">
                        <p class="text-2xl font-bold leading-tight">
                            AthleteGum solves this by holding funds securely until work is completed and approved.
                        </p>
                    </div>
                </div>
            </section>

            {{-- Image break --}}
            <section class="relative min-h-[500px] overflow-hidden py-28 text-white lg:py-36">
                <img
                    src="https://images.unsplash.com/photo-1758691736843-90f58dce465e?auto=format&amp;fit=crop&amp;w=2000&amp;q=80"
                    alt=""
                    class="absolute inset-0 h-full w-full object-cover object-center grayscale"
                    loading="lazy"
                    decoding="async"
                >
                <div class="absolute inset-0 bg-black/60"></div>
                <div class="relative z-10 flex min-h-[360px] flex-col items-center justify-center px-6 py-16 text-center lg:min-h-[420px] lg:py-24">
                    <p class="text-5xl font-bold uppercase text-white lg:text-7xl" style="color: #ffffff !important;">Not a marketplace.</p>
                    <p class="mt-4 text-2xl text-white/80" style="color: rgba(255,255,255,0.8) !important;">A deal execution platform.</p>
                </div>
            </section>

            {{-- How it works overview --}}
            <section class="border-y border-white/10 bg-black py-28 text-white lg:py-36">
                <div class="mx-auto max-w-7xl px-6 py-12 sm:px-8 lg:px-12 lg:py-16">
                    <h2 class="text-left text-5xl font-bold tracking-tight lg:text-6xl" style="color: #ffffff !important;">How AthleteGum Works</h2>
                    @php
                        $steps = [
                            'Businesses create deals and define deliverables',
                            'Funds are held securely until work is done',
                            'Athletes submit deliverables directly in the platform',
                            'Payments are released only after approval',
                        ];
                    @endphp
                    <div class="mt-24 grid grid-cols-1 gap-16 sm:grid-cols-2 lg:mt-32 lg:grid-cols-4 lg:gap-12">
                        @foreach ($steps as $i => $text)
                            <div class="about-reveal-up flex flex-col items-center text-center" data-about-reveal style="--about-delay: {{ $i * 0.1 }}s">
                                <p class="text-7xl font-bold leading-none lg:text-8xl" style="color: rgba(255,255,255,0.5) !important;" aria-hidden="true">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</p>
                                <p class="mt-8 max-w-[14rem] text-base font-medium leading-relaxed sm:max-w-none lg:mt-10 lg:text-lg" style="color: #ffffff !important;">{{ $text }}</p>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-28 text-center text-base text-white/50 lg:mt-36 lg:text-lg" style="color: rgba(255,255,255,0.5) !important;">No contracts to chase. No awkward payment conversations.</p>
                </div>
            </section>

            {{-- Who it's for --}}
            <section class="bg-white py-28 text-black lg:py-36">
                <div class="mx-auto max-w-6xl px-6 py-12 sm:px-8 lg:px-12 lg:py-16">
                    <h2 class="text-5xl font-bold lg:text-7xl">Who AthleteGum Is For</h2>
                    <div class="mt-24 grid gap-16 lg:mt-32 lg:grid-cols-2">
                        <div class="about-reveal-l30" data-about-reveal>
                            <div class="flex h-16 w-16 items-center justify-center bg-black text-white">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 22V4a2 2 0 012-2h8a2 2 0 012 2v18z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12h12M6 9h12M6 6h12" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 22v-4h6v4" />
                                </svg>
                            </div>
                            <h3 class="mt-6 text-3xl font-bold">For Businesses</h3>
                            <ul class="mt-6 space-y-4">
                                @foreach (['Small businesses', 'Local brands', 'Agencies and collectives', 'Startups or recurring NIL campaigns'] as $item)
                                    <li class="flex items-center gap-3">
                                        <span class="h-2 w-2 shrink-0 bg-black" aria-hidden="true"></span>
                                        <span class="text-lg font-medium">{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="about-reveal-r30" data-about-reveal>
                            <div class="flex h-16 w-16 items-center justify-center bg-black text-white">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                                </svg>
                            </div>
                            <h3 class="mt-6 text-3xl font-bold">For Athletes</h3>
                            <ul class="mt-6 space-y-4">
                                @foreach (['College athletes', 'Creators and influencers', 'Individuals or teams doing real promotional work'] as $item)
                                    <li class="flex items-center gap-3">
                                        <span class="h-2 w-2 shrink-0 bg-black" aria-hidden="true"></span>
                                        <span class="text-lg font-medium">{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Our focus --}}
            <section class="border-t border-white/10 bg-black py-28 text-white lg:py-36">
                <div class="mx-auto max-w-6xl px-6 py-12 sm:px-8 lg:px-12 lg:py-16">
                    <div class="text-center">
                        <h2 class="text-5xl font-bold text-white lg:text-7xl" style="color: #ffffff !important;">Our Focus</h2>
                        <p class="mx-auto mt-10 max-w-2xl text-2xl text-white/60 lg:mt-12" style="color: rgba(255,255,255,0.6) !important;">Four principles that guide everything we build</p>
                    </div>
                    @php
                        $values = [
                            [
                                'title' => 'Trust',
                                'body' => 'Escrow protection means both sides are protected. No one gets burned.',
                                'icon' => 'shield',
                            ],
                            [
                                'title' => 'Transparency',
                                'body' => 'See exactly what\'s expected, when it\'s due, and how much you\'ll pay or earn.',
                                'icon' => 'eye',
                            ],
                            [
                                'title' => 'Clear expectations',
                                'body' => 'Define deliverables upfront. No confusion, no surprises on either side.',
                                'icon' => 'check',
                            ],
                            [
                                'title' => 'Fair payments',
                                'body' => 'Athletes get paid quickly once work is approved. Businesses only pay for completed work.',
                                'icon' => 'dollar',
                            ],
                        ];
                    @endphp
                    <div class="mt-24 grid gap-8 md:grid-cols-2 lg:mt-32">
                        @foreach ($values as $i => $v)
                            <div
                                class="about-reveal-up group border-2 border-white p-8 transition-colors duration-200 hover:bg-white hover:text-black"
                                data-about-reveal
                                style="--about-delay: {{ $i * 0.1 }}s"
                            >
                                <div class="flex h-16 w-16 items-center justify-center bg-white text-black transition-colors duration-200 group-hover:bg-black group-hover:text-white">
                                    @if ($v['icon'] === 'shield')
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4v6c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4z" />
                                        </svg>
                                    @elseif ($v['icon'] === 'eye')
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                    @elseif ($v['icon'] === 'check')
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                            <circle cx="12" cy="12" r="10" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4" />
                                        </svg>
                                    @else
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
                                        </svg>
                                    @endif
                                </div>
                                <h3 class="mt-6 text-3xl font-bold text-white transition-colors group-hover:text-black">{{ $v['title'] }}</h3>
                                <p class="mt-4 text-lg text-white/70 transition-colors duration-200 group-hover:text-black/70">{{ $v['body'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Final CTA --}}
            <section class="bg-white py-28 text-black lg:py-36">
                <div class="mx-auto max-w-4xl px-6 py-12 text-center sm:px-8 lg:px-12 lg:py-16">
                    <h2 class="text-5xl font-bold leading-tight lg:text-7xl">AthleteGum is not a marketplace.</h2>
                    <p class="mx-auto mt-6 max-w-3xl text-2xl leading-relaxed text-black/70">
                        It&apos;s a deal execution platform built for trust, transparency, and getting athletes paid fairly.
                    </p>
                    <div class="mt-12 flex flex-col justify-center gap-4 sm:flex-row sm:gap-6">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-none bg-black px-12 py-5 text-sm font-bold text-white transition hover:bg-zinc-900">
                            Start Your First Deal
                        </a>
                        <a href="{{ route('pages.how-it-works') }}" class="inline-flex items-center justify-center rounded-none border-2 border-black px-12 py-5 text-sm font-bold text-black transition hover:bg-black hover:text-white">
                            Learn How It Works
                        </a>
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

        <script>
            (function () {
                function markVisible(el) {
                    el.classList.add('about-visible');
                }
                document.querySelectorAll('[data-about-hero]').forEach(function (el) {
                    requestAnimationFrame(function () {
                        markVisible(el);
                    });
                });
                var io = new IntersectionObserver(
                    function (entries) {
                        entries.forEach(function (entry) {
                            if (entry.isIntersecting) {
                                markVisible(entry.target);
                                io.unobserve(entry.target);
                            }
                        });
                    },
                    { threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
                );
                document.querySelectorAll('[data-about-reveal]').forEach(function (el) {
                    io.observe(el);
                });
            })();
        </script>
    </body>
</html>
