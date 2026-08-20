@extends('layouts.app')

@section('content')
<style>
    body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
    .register-shell {
        background:
            radial-gradient(circle at 8% 25%, rgba(186, 218, 255, .48) 0 85px, transparent 86px),
            radial-gradient(circle at 94% 12%, rgba(169, 210, 255, .42) 0 180px, transparent 181px),
            linear-gradient(145deg, #fafdff 0%, #eef7ff 54%, #d7ebff 100%);
    }
    .register-card { box-shadow: 0 24px 65px rgba(16, 75, 145, .16); }
    .register-input:focus { border-color: #1769ed; box-shadow: 0 0 0 3px rgba(23, 105, 237, .11); outline: none; }
</style>

<main class="register-shell relative min-h-screen w-full overflow-hidden px-4 py-8 text-[#0b2b69] sm:px-6 lg:py-10">
    <div class="pointer-events-none absolute -right-40 top-1/4 h-[650px] w-[330px] rotate-[28deg] rounded-[50%] bg-gradient-to-b from-[#2f8bf5] to-[#0861e8]"></div>
    <div class="pointer-events-none absolute -left-36 bottom-[-150px] h-80 w-80 rounded-full bg-blue-200/40"></div>

    <div class="relative z-10 mx-auto max-w-6xl">
        <a href="{{ route('home') }}" class="mx-auto flex w-fit items-center gap-3" aria-label="Back to QuickWash Express home">
            <img src="{{ asset('images/quickwash-logo.png') }}" alt="QuickWash logo" class="h-20 w-20 rounded-full object-contain">
            <span>
                <span class="block text-3xl font-black leading-none tracking-tight text-[#0a2d75]">QuickWash</span>
                <span class="mt-1 block text-center text-[.65rem] font-extrabold uppercase tracking-[.4em] text-[#1265e8]">Express</span>
            </span>
        </a>

        <div class="mt-5 text-center">
            <h1 class="text-3xl font-black tracking-tight text-[#092d6c] sm:text-4xl">Create Your Account</h1>
            <p class="mt-2 text-sm font-medium text-slate-500">Fresh laundry is only a few steps away</p>
        </div>

        <div class="register-card mx-auto mt-7 grid max-w-5xl overflow-hidden rounded-2xl bg-white lg:grid-cols-[38%_62%]">
            <section class="relative hidden overflow-hidden bg-gradient-to-br from-[#0d67eb] to-[#0849b8] px-10 py-12 text-white lg:block">
                <div class="relative z-10">
                    <span class="inline-flex rounded-full bg-white/15 px-4 py-1.5 text-xs font-bold uppercase tracking-widest">New customer</span>
                    <h2 class="mt-6 text-3xl font-black leading-tight">More time for life.<br><span class="text-blue-200">Less time on laundry.</span></h2>
                    <p class="mt-5 text-sm leading-6 text-blue-100">Create your account to schedule pickups, follow your orders, and receive clean clothes at your doorstep.</p>

                    <div class="mt-10 space-y-5 text-sm font-semibold">
                        @foreach ([
                            'Doorstep pickup and delivery',
                            'Live order status updates',
                            'Careful, quality cleaning',
                        ] as $benefit)
                            <div class="flex items-center gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/15">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
                                </span>
                                {{ $benefit }}
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="pointer-events-none absolute -bottom-24 -right-20 h-72 w-72 rounded-full border-[45px] border-white/10"></div>
                <div class="pointer-events-none absolute bottom-24 right-10 h-20 w-20 rounded-full bg-white/10"></div>
            </section>

            <section class="px-6 py-8 sm:px-10 lg:px-12">
                <h2 class="text-xl font-black text-[#0b2b69]">Personal Information</h2>
                <p class="mt-1 text-xs font-medium text-slate-500">Fill in your details to get started</p>

                @if ($errors->any())
                    <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                        <p class="font-bold">Please check the following:</p>
                        <ul class="mt-1 list-inside list-disc">
                            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('customer.register') }}" class="mt-6 space-y-4">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div><label for="first_name" class="mb-1.5 block text-xs font-bold text-[#16366d]">First Name</label><input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name" placeholder="First name" class="register-input h-11 w-full rounded-lg border border-slate-300 px-4 text-sm text-slate-700 placeholder:text-slate-400"></div>
                        <div><label for="last_name" class="mb-1.5 block text-xs font-bold text-[#16366d]">Last Name</label><input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name" placeholder="Last name" class="register-input h-11 w-full rounded-lg border border-slate-300 px-4 text-sm text-slate-700 placeholder:text-slate-400"></div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div><label for="email" class="mb-1.5 block text-xs font-bold text-[#16366d]">Email Address</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@example.com" class="register-input h-11 w-full rounded-lg border border-slate-300 px-4 text-sm text-slate-700 placeholder:text-slate-400"></div>
                        <div><label for="phone" class="mb-1.5 block text-xs font-bold text-[#16366d]">Phone Number</label><input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel" inputmode="numeric" minlength="11" maxlength="11" pattern="[0-9]{11}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)" placeholder="11-digit phone number" class="register-input h-11 w-full rounded-lg border border-slate-300 px-4 text-sm text-slate-700 placeholder:text-slate-400"><p class="mt-1 text-[.7rem] text-slate-400">Enter exactly 11 digits.</p></div>
                    </div>
                    <div><label for="address" class="mb-1.5 block text-xs font-bold text-[#16366d]">Complete Address</label><textarea id="address" name="address" rows="2" required autocomplete="street-address" placeholder="House number, street, and landmark" class="register-input w-full resize-none rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400">{{ old('address') }}</textarea></div>
                    <div><label for="barangay" class="mb-1.5 block text-xs font-bold text-[#16366d]">Barangay</label><select id="barangay" name="barangay" required class="register-input h-11 w-full rounded-lg border border-slate-300 bg-white px-4 text-sm text-slate-700"><option value="">Select your barangay</option>@foreach (['Balidbid','Bantigue','Langub','Maricaban','Okoy','Poblacion','Pooc','Talisay'] as $b)<option value="{{ $b }}" @selected(old('barangay') === $b)>{{ $b }}</option>@endforeach</select></div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div><label for="password" class="mb-1.5 block text-xs font-bold text-[#16366d]">Password</label><input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Create password" class="register-input h-11 w-full rounded-lg border border-slate-300 px-4 text-sm text-slate-700 placeholder:text-slate-400"></div>
                        <div><label for="password_confirmation" class="mb-1.5 block text-xs font-bold text-[#16366d]">Confirm Password</label><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password" class="register-input h-11 w-full rounded-lg border border-slate-300 px-4 text-sm text-slate-700 placeholder:text-slate-400"></div>
                    </div>
                    <button type="submit" class="flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-[#0d5fe9] text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition hover:-translate-y-0.5 hover:bg-[#084fc9]">
                        Create My Account
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </form>

                <p class="mt-5 text-center text-sm text-slate-500">Already have an account? <a href="{{ route('customer.login') }}" class="font-bold text-[#0d5fe9] hover:underline">Log in</a></p>
            </section>
        </div>

        <p class="mt-6 text-center text-xs font-medium text-slate-500">&copy; {{ date('Y') }} QuickWash Express. All rights reserved.</p>
    </div>
</main>
@endsection
