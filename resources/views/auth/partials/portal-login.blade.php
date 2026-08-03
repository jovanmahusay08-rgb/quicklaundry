@php
    $selectedPortal = $selectedPortal ?? 'customer';
@endphp

<style>
    body {
        font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .auth-shell {
        background:
            radial-gradient(circle at 9% 38%, rgba(186, 218, 255, .44) 0 74px, transparent 75px),
            radial-gradient(circle at 91% 14%, rgba(169, 210, 255, .36) 0 185px, transparent 186px),
            radial-gradient(circle at 7% 91%, rgba(181, 218, 255, .38) 0 110px, transparent 111px),
            linear-gradient(145deg, #fafdff 0%, #eef7ff 54%, #d7ebff 100%);
    }

    .auth-card {
        box-shadow: 0 24px 65px rgba(16, 75, 145, .16);
    }

    .auth-input:focus {
        border-color: #1769ed;
        box-shadow: 0 0 0 3px rgba(23, 105, 237, .11);
        outline: none;
    }

    .bubble {
        background: radial-gradient(circle at 32% 28%, rgba(255, 255, 255, .95), rgba(167, 215, 255, .2) 55%, rgba(90, 165, 244, .24));
        box-shadow: inset -2px -3px 8px rgba(82, 153, 226, .13);
    }
</style>

<div class="auth-shell relative min-h-screen w-full overflow-hidden px-4 py-10 text-[#0b2b69] sm:px-6 lg:py-12">
    <div class="pointer-events-none absolute -right-36 top-1/3 h-[640px] w-[330px] rotate-[28deg] rounded-[50%] bg-gradient-to-b from-[#2f8bf5] to-[#0861e8] opacity-95"></div>
    <div class="pointer-events-none absolute -left-32 bottom-[-135px] h-72 w-72 rounded-full bg-blue-200/35"></div>
    <span class="bubble pointer-events-none absolute left-[9%] top-[26%] h-6 w-6 rounded-full"></span>
    <span class="bubble pointer-events-none absolute right-[13%] top-[8%] h-7 w-7 rounded-full"></span>
    <span class="bubble pointer-events-none absolute right-[20%] top-[29%] h-4 w-4 rounded-full"></span>

    <div class="relative z-10 mx-auto max-w-5xl">
        <a href="{{ route('home') }}" class="mx-auto flex w-fit items-center gap-4" aria-label="Back to QuickWash Express home">
            <span class="relative flex h-16 w-16 items-center justify-center rounded-full border-[6px] border-[#1265e8] text-[#1265e8]">
                <span class="absolute -left-5 top-1 h-1.5 w-5 rounded-full bg-[#46a6ff]"></span>
                <span class="absolute -left-7 top-5 h-1.5 w-7 rounded-full bg-[#1265e8]"></span>
                <span class="absolute -left-4 bottom-1 h-1.5 w-4 rounded-full bg-[#46a6ff]"></span>
                <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 13c2-3 4-4 7-2 3 2 5 1 9-2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-4Z" fill="currentColor" opacity=".9"/>
                    <path d="M6.5 7.5h.01M17.5 6.5h.01" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
            </span>
            <span>
                <span class="block text-3xl font-black leading-none tracking-tight text-[#0a2d75] sm:text-4xl">QuickWash</span>
                <span class="mt-1.5 block text-center text-xs font-extrabold uppercase tracking-[.4em] text-[#1265e8] sm:text-sm">Express</span>
            </span>
        </a>

        <div class="mt-8 text-center">
            <h1 class="text-3xl font-black tracking-tight text-[#092d6c] sm:text-4xl">Welcome Back!</h1>
            <p class="mt-2 text-sm font-medium text-slate-500 sm:text-base">Log in to your account to continue</p>
        </div>

        <div class="auth-card mx-auto mt-9 grid max-w-4xl overflow-hidden rounded-2xl bg-white md:grid-cols-[46%_54%]">
            <section class="relative min-h-[630px] overflow-hidden bg-gradient-to-br from-[#eff8ff] to-[#dceeff] px-8 pb-6 pt-10 sm:px-10">
                <div class="relative z-10">
                    <h2 class="text-2xl font-black leading-tight text-[#0b3d91]">
                        Clean Clothes.<br>
                        <span class="text-[#1265e8]">Happy Life.</span>
                    </h2>
                    <p class="mt-4 max-w-[260px] text-sm font-medium leading-6 text-slate-600">
                        QuickWash Express makes laundry simple and convenient. We pick up, clean with care, and deliver fresh clothes right to your door.
                    </p>
                </div>

                <img src="{{ asset('images/auth/login-laundry-scene.png') }}" alt="Washing machine, laundry basket, and neatly folded towels" class="absolute inset-x-0 bottom-14 w-full" width="1024" height="1536">

                <div class="absolute inset-x-0 bottom-0 z-10 grid grid-cols-3 gap-3 border-t border-blue-100/80 bg-white/88 px-4 py-5 text-center text-[.65rem] font-bold leading-4 text-[#17366e] backdrop-blur sm:px-6">
                    <div class="flex flex-col items-center">
                        <svg class="mb-2 h-7 w-7 text-[#1265e8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M3 15h3l2-5h7l3 5h3M5 15v3h14v-3M8 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"></path></svg>
                        <span>Free Pickup<br>at Your Doorstep</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <svg class="mb-2 h-7 w-7 text-[#1265e8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m12 2 8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-4Z"></path><path d="m8.5 12 2.2 2.2 4.8-5"></path></svg>
                        <span>Quality Clean<br>Every Time</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <svg class="mb-2 h-7 w-7 text-[#1265e8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>
                        <span>On-Time<br>Delivery</span>
                    </div>
                </div>
            </section>

            <section class="flex items-center px-7 py-10 sm:px-11">
                <div class="w-full">
                    <h2 class="text-2xl font-black text-[#0b2b69]">Log In</h2>
                    <p class="mt-1 text-xs font-medium text-slate-500">Enter your credentials to access your account</p>

                    @if ($errors->any())
                        <div class="mt-5 flex gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                            <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v6M12 17h.01"></path></svg>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route($selectedPortal.'.login') }}" class="mt-7 space-y-5">
                        @csrf
                        <div>
                            <label for="email" class="mb-2 block text-xs font-bold text-[#16366d]">Email Address</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m4 7 8 6 8-6"></path></svg>
                                </span>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="Enter your email" class="auth-input h-12 w-full rounded-lg border border-slate-300 bg-white pl-12 pr-4 text-sm text-slate-700 transition placeholder:text-slate-400">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-xs font-bold text-[#16366d]">Password</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path></svg>
                                </span>
                                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" class="auth-input h-12 w-full rounded-lg border border-slate-300 bg-white pl-12 pr-12 text-sm text-slate-700 transition placeholder:text-slate-400">
                                <button id="toggle-password" type="button" class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-[#1769ed]" aria-label="Show password">
                                    <svg id="eye-open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <svg id="eye-closed" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m3 3 18 18M10.6 10.7a2 2 0 0 0 2.7 2.7M9.9 5.2A10.7 10.7 0 0 1 12 5c6.5 0 10 7 10 7a17 17 0 0 1-2.1 3M6.6 6.6C3.6 8.5 2 12 2 12s3.5 7 10 7c1.4 0 2.7-.3 3.8-.7"></path></svg>
                                </button>
                            </div>
                        </div>

                        <label class="flex cursor-pointer items-center gap-2 text-xs font-medium text-slate-600">
                            <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="h-4 w-4 rounded border-slate-300 accent-[#1265e8]">
                            Remember me
                        </label>

                        <button type="submit" class="flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-[#0d5fe9] text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition hover:-translate-y-0.5 hover:bg-[#084fc9]">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3M14 4h5a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-5"></path></svg>
                            Log In
                        </button>
                    </form>

                    <div class="my-6 flex items-center gap-4 text-xs text-slate-400">
                        <span class="h-px flex-1 bg-slate-200"></span>
                        <span>or</span>
                        <span class="h-px flex-1 bg-slate-200"></span>
                    </div>

                    <a href="{{ route('customer.register') }}" class="flex h-12 w-full items-center justify-center gap-2 rounded-lg border border-[#1769ed] bg-white text-sm font-bold text-[#0d5fe9] transition hover:bg-blue-50">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="9" cy="7" r="3"></circle><path d="M3 20a6 6 0 0 1 12 0M19 8v6M16 11h6"></path></svg>
                        Create an Account
                    </a>

                    <a href="{{ route('home') }}" class="mt-5 flex items-center justify-center gap-2 text-xs font-semibold text-slate-500 transition hover:text-[#1265e8]">
                        <span aria-hidden="true">←</span> Back to home
                    </a>
                </div>
            </section>
        </div>

        <p class="mt-8 text-center text-xs font-medium text-slate-500">&copy; {{ date('Y') }} QuickWash Express. All rights reserved.</p>
    </div>
</div>

<script>
    (() => {
        const password = document.getElementById('password');
        const toggle = document.getElementById('toggle-password');
        const eyeOpen = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');

        toggle?.addEventListener('click', () => {
            const isHidden = password.type === 'password';
            password.type = isHidden ? 'text' : 'password';
            toggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            eyeOpen.classList.toggle('hidden', isHidden);
            eyeClosed.classList.toggle('hidden', !isHidden);
        });
    })();
</script>
