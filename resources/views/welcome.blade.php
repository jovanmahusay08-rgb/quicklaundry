@extends('layouts.app')

@section('content')
<style>
    html {
        scroll-behavior: smooth;
    }

    body {
        font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .landing-shadow {
        box-shadow: 0 16px 42px rgba(15, 74, 145, .11);
    }

    .portal-card {
        box-shadow: 0 12px 30px rgba(15, 74, 145, .10);
    }

    .portal-card:hover {
        box-shadow: 0 18px 38px rgba(15, 74, 145, .17);
        transform: translateY(-4px);
    }

    .hero-art {
        width: 122%;
        max-width: none;
        transform: translateX(-15%);
    }

    .portal-wave {
        clip-path: ellipse(70% 56% at 50% 100%);
    }

    @media (max-width: 1023px) {
        .hero-art {
            width: 112%;
            transform: translateX(-6%);
        }
    }

    @media (max-width: 767px) {
        .hero-art {
            width: 100%;
            transform: none;
        }
    }
</style>

<div id="home" class="min-h-screen w-full overflow-x-hidden bg-white text-[#0b2b69]">
    <header class="sticky top-0 z-50 border-b border-blue-100 bg-white/95 shadow-[0_4px_22px_rgba(22,91,170,.08)] backdrop-blur">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-5 sm:px-8">
            <a href="#home" class="flex shrink-0 items-center gap-3" aria-label="QuickWash Express home">
                <span class="relative flex h-12 w-12 items-center justify-center rounded-full border-[5px] border-[#1265e8] text-[#1265e8]">
                    <span class="absolute -left-4 top-1 h-1 w-4 rounded-full bg-[#46a6ff]"></span>
                    <span class="absolute -left-5 top-4 h-1 w-5 rounded-full bg-[#1265e8]"></span>
                    <span class="absolute -left-3 bottom-1 h-1 w-3 rounded-full bg-[#46a6ff]"></span>
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 13c2-3 4-4 7-2 3 2 5 1 9-2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-4Z" fill="currentColor" opacity=".9"/>
                        <path d="M6.5 7.5h.01M17.5 6.5h.01" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </span>
                <span>
                    <span class="block text-2xl font-black leading-none tracking-tight text-[#0a2d75] sm:text-[1.7rem]">QuickWash</span>
                    <span class="mt-1 block text-center text-[.68rem] font-extrabold uppercase tracking-[.34em] text-[#1265e8]">Express</span>
                </span>
            </a>

            <nav class="hidden items-center gap-9 text-sm font-semibold text-[#102f70] lg:flex" aria-label="Main navigation">
                <a href="#home" class="relative py-7 text-[#1265e8] after:absolute after:inset-x-0 after:bottom-0 after:h-1 after:rounded-t-full after:bg-[#1265e8]">Home</a>
                <a href="#services" class="py-7 transition hover:text-[#1265e8]">Services</a>
                <a href="#how-it-works" class="py-7 transition hover:text-[#1265e8]">How It Works</a>
                <a href="#about" class="py-7 transition hover:text-[#1265e8]">About Us</a>
                <a href="#contact" class="py-7 transition hover:text-[#1265e8]">Contact</a>
            </nav>

            <span class="hidden w-[188px] lg:block" aria-hidden="true"></span>

            <details class="relative lg:hidden">
                <summary class="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-xl bg-blue-50 text-[#0b4fb8] [&::-webkit-details-marker]:hidden" aria-label="Open navigation">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h16"></path>
                    </svg>
                </summary>
                <nav class="absolute right-0 top-14 w-56 rounded-2xl border border-blue-100 bg-white p-3 text-sm font-semibold shadow-xl">
                    <a href="#home" class="block rounded-lg px-4 py-2.5 text-[#1265e8] hover:bg-blue-50">Home</a>
                    <a href="#services" class="block rounded-lg px-4 py-2.5 hover:bg-blue-50">Services</a>
                    <a href="#how-it-works" class="block rounded-lg px-4 py-2.5 hover:bg-blue-50">How It Works</a>
                    <a href="#about" class="block rounded-lg px-4 py-2.5 hover:bg-blue-50">About Us</a>
                    <a href="#contact" class="block rounded-lg px-4 py-2.5 hover:bg-blue-50">Contact</a>
                </nav>
            </details>
        </div>
    </header>

    <main>
        <section class="relative overflow-hidden bg-gradient-to-br from-white via-[#f4faff] to-[#dcedff]">
            <div class="pointer-events-none absolute -right-28 top-12 h-80 w-80 rounded-full bg-blue-300/20 blur-2xl"></div>
            <div class="pointer-events-none absolute left-[45%] top-20 h-56 w-56 rounded-full border-[48px] border-blue-200/25"></div>

            <div class="mx-auto grid min-h-[545px] max-w-7xl items-center gap-7 px-5 py-12 sm:px-8 md:grid-cols-[45%_55%] md:py-10">
                <div class="relative z-20">
                    <span class="inline-flex rounded-full bg-[#dceeff] px-4 py-1.5 text-xs font-extrabold uppercase tracking-wide text-[#1265e8]">
                        Laundry pickup &amp; delivery
                    </span>
                    <h1 class="mt-5 text-[2.55rem] font-black leading-[1.08] tracking-[-.035em] text-[#092d6c] sm:text-5xl lg:text-[3.6rem]">
                        Fresh Clothes.<br>
                        Fast Service.<br>
                        <span class="text-[#1265e8]">Right to Your Door.</span>
                    </h1>
                    <p class="mt-5 max-w-xl text-base font-medium leading-7 text-slate-600 lg:text-lg">
                        QuickWash Express makes laundry easy and hassle-free. We pick up, clean with care, and deliver fresh, folded clothes right to you.
                    </p>

                    <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('customer.login') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-lg bg-[#0d5fe9] px-7 text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition hover:-translate-y-0.5 hover:bg-[#084fc9]">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                <path d="M8 3v4M16 3v4M3 10h18M8 14h3M8 17h5"></path>
                            </svg>
                            Book a Service
                        </a>
                        <a href="{{ route('customer.login') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-lg border-2 border-[#2873ed] bg-white/80 px-7 text-sm font-bold text-[#145ccc] transition hover:-translate-y-0.5 hover:bg-blue-50">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path>
                                <circle cx="12" cy="10" r="2.5"></circle>
                            </svg>
                            Track My Order
                        </a>
                        <a href="{{ route('app.download') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-lg border-2 border-emerald-500 bg-emerald-600 px-7 text-sm font-bold text-white shadow-lg shadow-emerald-500/20 transition hover:-translate-y-0.5 hover:bg-emerald-700" download>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M12 3v12"></path>
                                <path d="m7 10 5 5 5-5"></path>
                                <path d="M5 21h14"></path>
                            </svg>
                            Download App
                        </a>
                    </div>

                    <div class="mt-9 grid grid-cols-2 gap-x-5 gap-y-4 text-[.74rem] font-semibold leading-5 text-[#17366e] sm:grid-cols-4">
                        <div class="flex items-center gap-2">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 border-blue-500 text-blue-600">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 15h3l2-5h7l3 5h3M5 15v3h14v-3M8 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"></path></svg>
                            </span>
                            <span>Free Pickup<br>at Your Doorstep</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center text-blue-600">
                                <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m12 2 8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-4Z"></path><path d="m8.5 12 2.2 2.2 4.8-5"></path></svg>
                            </span>
                            <span>Quality Clean<br>Every Time</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 border-blue-500 text-blue-600">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 5v7l4 2"></path></svg>
                            </span>
                            <span>On-Time<br>Delivery</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center text-blue-600">
                                <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 12 12 3h6l3 3v6l-9 9-9-9Z"></path><circle cx="16.5" cy="7.5" r="1"></circle><path d="m9 11 6 6"></path></svg>
                            </span>
                            <span>Affordable<br>Pricing</span>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 self-end md:self-center">
                    <img src="{{ asset('images/landing/quickwash-hero.png') }}" alt="Modern washing machine with a basket of freshly folded blue and white towels" class="hero-art block" width="1536" height="1024">

                    <div class="landing-shadow absolute right-1 top-[28%] hidden w-52 rounded-2xl bg-white/95 p-5 backdrop-blur xl:block">
                        <div class="flex gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#1265e8] text-white">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 15h3l2-5h7l3 5h3M5 15v3h14v-3M8 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"></path></svg>
                            </span>
                            <div>
                                <p class="font-extrabold leading-5 text-[#0b2b69]">We Pick Up &amp;<br>Deliver for You!</p>
                                <p class="mt-3 text-xs leading-5 text-slate-500">Save time. Enjoy life. Let us handle your laundry.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="portals" class="relative overflow-hidden bg-white px-5 pb-24 pt-10 sm:px-8">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-16 bg-blue-300/55 portal-wave"></div>
            <div class="pointer-events-none absolute inset-x-0 -bottom-5 h-16 bg-[#1265e8] portal-wave"></div>

            <div class="relative z-10 mx-auto max-w-6xl">
                <div class="text-center">
                    <span class="inline-flex rounded-full bg-[#e2f1ff] px-6 py-1 text-xs font-extrabold uppercase tracking-wide text-[#1265e8]">Choose your portal</span>
                    <h2 class="mt-2 text-3xl font-black tracking-tight text-[#092d6c]">Access Your Portal</h2>
                    <p class="mt-1 text-sm font-medium text-slate-500">Select your portal to get started</p>
                </div>

                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    <a href="{{ route('admin.login') }}" class="portal-card group rounded-xl border border-t-[3px] border-blue-100 border-t-[#1265e8] bg-white p-6 transition duration-300">
                        <div class="flex items-start gap-5">
                            <span class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-blue-100 text-[#1265e8]">
                                <svg class="h-10 w-10" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m12 2 8 3v6c0 5.2-3.4 9-8 11-4.6-2-8-5.8-8-11V5l8-3Zm-3.8 8.3 1.5 5.2 2.3-2 2.3 2 1.5-5.2-2.5 1-1.3-3-1.3 3-2.5-1Z"></path></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-base font-extrabold uppercase text-[#0c5bd9]">Admin Portal</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Manage the system, users, bookings, and reports.</p>
                                <span class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#0d5fe9] px-4 py-3 text-sm font-bold text-white transition group-hover:bg-[#084fc9]">
                                    Go to Admin Portal <span aria-hidden="true">→</span>
                                </span>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('customer.login') }}" class="portal-card group rounded-xl border border-t-[3px] border-emerald-100 border-t-emerald-500 bg-white p-6 transition duration-300">
                        <div class="flex items-start gap-5">
                            <span class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                                <svg class="h-11 w-11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="7" r="4"></circle><path d="M4 21a8 8 0 0 1 16 0H4Z"></path></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-base font-extrabold uppercase text-emerald-600">Customer Portal</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Book services, track orders, and manage your profile.</p>
                                <span class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-3 text-sm font-bold text-white transition group-hover:bg-emerald-700">
                                    Go to Customer Portal <span aria-hidden="true">→</span>
                                </span>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('staff.login') }}" class="portal-card group rounded-xl border border-t-[3px] border-violet-100 border-t-violet-500 bg-white p-6 transition duration-300">
                        <div class="flex items-start gap-5">
                            <span class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-violet-50 text-violet-600">
                                <svg class="h-11 w-11" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9 3h6l1 3h2a2 2 0 0 1 2 2v2H4V8a2 2 0 0 1 2-2h2l1-3Zm3 8a4 4 0 1 1 0 8 4 4 0 0 1 0-8Zm-8 11c.8-2.5 3.8-3 8-3s7.2.5 8 3H4Z"></path></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-base font-extrabold uppercase text-violet-600">Staff Portal</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Manage bookings, orders, and daily operations.</p>
                                <span class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-violet-600 px-4 py-3 text-sm font-bold text-white transition group-hover:bg-violet-700">
                                    Go to Staff Portal <span aria-hidden="true">→</span>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <section id="services" class="scroll-mt-20 bg-[#f5faff] px-5 py-20 sm:px-8">
            <div class="mx-auto max-w-6xl text-center">
                <span class="text-xs font-extrabold uppercase tracking-[.2em] text-[#1265e8]">Our services</span>
                <h2 class="mt-3 text-3xl font-black text-[#092d6c]">Everything your laundry needs</h2>
                <div class="mt-10 grid gap-5 md:grid-cols-3">
                    <div class="rounded-2xl bg-white p-7 shadow-sm"><p class="text-lg font-extrabold">Wash &amp; Fold</p><p class="mt-2 text-sm leading-6 text-slate-500">Carefully washed, dried, folded, and ready for your closet.</p></div>
                    <div class="rounded-2xl bg-white p-7 shadow-sm"><p class="text-lg font-extrabold">Pickup Service</p><p class="mt-2 text-sm leading-6 text-slate-500">Convenient doorstep collection scheduled around your day.</p></div>
                    <div class="rounded-2xl bg-white p-7 shadow-sm"><p class="text-lg font-extrabold">Fresh Delivery</p><p class="mt-2 text-sm leading-6 text-slate-500">Clean clothes returned fresh, neat, and right on time.</p></div>
                </div>
            </div>
        </section>

        <section id="how-it-works" class="scroll-mt-20 bg-white px-5 py-20 sm:px-8">
            <div class="mx-auto max-w-5xl text-center">
                <span class="text-xs font-extrabold uppercase tracking-[.2em] text-[#1265e8]">How it works</span>
                <h2 class="mt-3 text-3xl font-black text-[#092d6c]">Clean clothes in three simple steps</h2>
                <div class="mt-12 grid gap-8 md:grid-cols-3">
                    <div><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-600 text-lg font-black text-white">1</span><h3 class="mt-4 font-extrabold">Book Online</h3><p class="mt-2 text-sm text-slate-500">Choose your laundry service and pickup schedule.</p></div>
                    <div><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-600 text-lg font-black text-white">2</span><h3 class="mt-4 font-extrabold">We Clean</h3><p class="mt-2 text-sm text-slate-500">Our team treats every item with expert care.</p></div>
                    <div><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-600 text-lg font-black text-white">3</span><h3 class="mt-4 font-extrabold">We Deliver</h3><p class="mt-2 text-sm text-slate-500">Receive fresh, folded laundry at your doorstep.</p></div>
                </div>
            </div>
        </section>

        <section id="about" class="scroll-mt-20 bg-[#092d6c] px-5 py-16 text-white sm:px-8">
            <div class="mx-auto grid max-w-6xl items-center gap-8 md:grid-cols-2">
                <div>
                    <span class="text-xs font-extrabold uppercase tracking-[.2em] text-blue-300">About QuickWash</span>
                    <h2 class="mt-3 text-3xl font-black">More time for what matters.</h2>
                </div>
                <p class="leading-7 text-blue-100">QuickWash Express combines convenient pickup, dependable cleaning, and on-time delivery to make laundry the easiest part of your week.</p>
            </div>
        </section>
    </main>

    <footer id="contact" class="scroll-mt-20 bg-[#061d48] px-5 py-8 text-blue-100 sm:px-8">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 text-sm sm:flex-row">
            <p class="font-bold text-white">QuickWash Express</p>
            <p>Fresh clothes, delivered with care.</p>
            <a href="#portals" class="font-bold text-blue-300 hover:text-white">Access your portal →</a>
        </div>
    </footer>
</div>
@endsection
