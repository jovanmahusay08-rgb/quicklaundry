@extends('layouts.app')

@section('content')
<main class="flex min-h-screen items-center justify-center bg-gradient-to-br from-sky-50 via-white to-blue-100 px-4 py-10">
    <section class="w-full max-w-md rounded-3xl border border-blue-100 bg-white p-7 shadow-2xl shadow-blue-900/10 sm:p-9">
        <a href="{{ route($portal.'.login') }}" class="mx-auto block w-fit" aria-label="Back to {{ ucfirst($portal) }} login">
            <img src="{{ asset('images/quickwash-logo.png') }}" alt="QuickWash logo" class="h-28 w-28 rounded-full object-contain">
        </a>

        @if ($step === 'request')
            <h1 class="mt-5 text-center text-2xl font-black text-slate-900">Forgot your password?</h1>
            <p class="mt-2 text-center text-sm leading-6 text-slate-500">Enter the email for your {{ ucfirst($portal) }} Portal account. We will send a six-digit verification code.</p>
        @elseif ($step === 'code')
            <h1 class="mt-5 text-center text-2xl font-black text-slate-900">Enter verification code</h1>
            <p class="mt-2 text-center text-sm leading-6 text-slate-500">Enter the six-digit code sent for <strong>{{ $email }}</strong>. It expires in 10 minutes.</p>
        @else
            <h1 class="mt-5 text-center text-2xl font-black text-slate-900">Create a new password</h1>
            <p class="mt-2 text-center text-sm leading-6 text-slate-500">Choose a secure password with at least eight characters.</p>
        @endif

        @if (session('status'))
            <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">{{ $errors->first() }}</div>
        @endif

        @if ($step === 'request')
            <form method="POST" action="{{ route($portal.'.password.email') }}" class="mt-7 space-y-5">
                @csrf
                <div>
                    <label for="email" class="mb-2 block text-sm font-bold text-slate-700">Email address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="h-12 w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                </div>
                <button class="h-12 w-full rounded-xl bg-sky-600 text-sm font-bold text-white transition hover:bg-sky-700" type="submit">Send verification code</button>
            </form>
        @elseif ($step === 'code')
            <form method="POST" action="{{ route($portal.'.password.verify') }}" class="mt-7 space-y-5">
                @csrf
                <div>
                    <label for="code" class="mb-2 block text-sm font-bold text-slate-700">Six-digit code</label>
                    <input id="code" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" name="code" required autofocus autocomplete="one-time-code" class="h-14 w-full rounded-xl border border-slate-300 px-4 text-center text-2xl font-bold tracking-[.45em] outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                </div>
                <button class="h-12 w-full rounded-xl bg-sky-600 text-sm font-bold text-white transition hover:bg-sky-700" type="submit">Verify code</button>
            </form>
            <a href="{{ route($portal.'.password.request') }}" class="mt-5 block text-center text-sm font-semibold text-sky-700 hover:underline">Request a new code</a>
        @else
            <form method="POST" action="{{ route($portal.'.password.update') }}" class="mt-7 space-y-5">
                @csrf
                <div>
                    <label for="password" class="mb-2 block text-sm font-bold text-slate-700">New password</label>
                    <input id="password" type="password" name="password" required autofocus autocomplete="new-password" class="h-12 w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                </div>
                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-bold text-slate-700">Confirm new password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="h-12 w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                </div>
                <button class="h-12 w-full rounded-xl bg-sky-600 text-sm font-bold text-white transition hover:bg-sky-700" type="submit">Reset password</button>
            </form>
        @endif

        <a href="{{ route($portal.'.login') }}" class="mt-6 block text-center text-sm font-semibold text-slate-500 hover:text-sky-700">Back to login</a>
    </section>
</main>
@endsection
