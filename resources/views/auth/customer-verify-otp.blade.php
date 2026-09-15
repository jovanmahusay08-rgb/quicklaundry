@extends('layouts.app')

@section('content')
<style>
    body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
    .otp-shell {
        background:
            radial-gradient(circle at 10% 20%, rgba(186, 218, 255, .45) 0 90px, transparent 91px),
            radial-gradient(circle at 90% 15%, rgba(169, 210, 255, .4) 0 160px, transparent 161px),
            linear-gradient(145deg, #fafdff 0%, #eef7ff 54%, #d7ebff 100%);
    }
    .otp-card { box-shadow: 0 24px 65px rgba(16, 75, 145, .16); }
    .otp-digit-input:focus { border-color: #1769ed; box-shadow: 0 0 0 4px rgba(23, 105, 237, .15); outline: none; }
</style>

<main class="otp-shell relative flex min-h-screen w-full items-center justify-center overflow-hidden px-4 py-10 text-[#0b2b69] sm:px-6">
    <div class="pointer-events-none absolute -right-36 top-1/4 h-[550px] w-[300px] rotate-[28deg] rounded-[50%] bg-gradient-to-b from-[#2f8bf5] to-[#0861e8] opacity-30"></div>
    <div class="pointer-events-none absolute -left-32 bottom-[-100px] h-72 w-72 rounded-full bg-blue-200/40"></div>

    <div class="relative z-10 w-full max-w-md">
        <a href="{{ route('home') }}" class="mx-auto flex w-fit items-center gap-3" aria-label="Back to QuickWash Express home">
            <img src="{{ asset('images/quickwash-logo.png') }}" alt="QuickWash logo" class="h-16 w-16 rounded-full object-contain">
            <span>
                <span class="block text-2xl font-black leading-none tracking-tight text-[#0a2d75]">QuickWash</span>
                <span class="mt-1 block text-center text-[.6rem] font-extrabold uppercase tracking-[.4em] text-[#1265e8]">Express</span>
            </span>
        </a>

        <div class="otp-card mt-7 overflow-hidden rounded-3xl border border-blue-100 bg-white p-6 shadow-2xl sm:p-8">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-[#0d67eb]">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="14" height="20" x="5" y="2" rx="2" ry="2"/>
                    <path d="M12 18h.01"/>
                    <path d="m9 10 2 2 4-4"/>
                </svg>
            </div>

            <div class="mt-4 text-center">
                <h1 class="text-2xl font-black tracking-tight text-[#092d6c]">Verify Mobile Number</h1>
                <p class="mt-2 text-xs leading-5 text-slate-500">
                    We sent a 6-digit SMS verification code via PhilSMS to<br>
                    <span class="font-bold text-[#0b2b69]">{{ $maskedPhone }}</span>
                </p>
            </div>

            @if (session('status'))
                <div class="mt-5 flex items-start gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800" role="status">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-5 flex items-start gap-2.5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700" role="alert">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('customer.register.otp.verify') }}" class="mt-6 space-y-5">
                @csrf
                <div>
                    <label for="otp_code" class="mb-2 block text-center text-xs font-bold uppercase tracking-wider text-slate-600">
                        Enter 6-Digit OTP Code
                    </label>
                    <input
                        id="otp_code"
                        type="text"
                        name="code"
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                        maxlength="6"
                        required
                        autofocus
                        autocomplete="one-time-code"
                        placeholder="••••••"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)"
                        class="otp-digit-input h-14 w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 text-center text-3xl font-extrabold tracking-[0.45em] text-[#092d6c] transition placeholder:tracking-normal placeholder:text-slate-300 sm:text-4xl"
                    >
                    <p class="mt-2 text-center text-[.7rem] text-slate-400">
                        Code expires in 10 minutes.
                    </p>
                </div>

                <button
                    type="submit"
                    class="flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-[#0d5fe9] text-sm font-bold text-white shadow-lg shadow-blue-500/25 transition hover:-translate-y-0.5 hover:bg-[#084fc9] active:translate-y-0"
                >
                    <span>Verify & Complete Registration</span>
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
            </form>

            <div class="mt-6 border-t border-slate-100 pt-5 text-center">
                <p class="text-xs text-slate-500">Didn't receive the SMS code?</p>

                <form method="POST" action="{{ route('customer.register.otp.resend') }}" class="mt-2 inline-block">
                    @csrf
                    <button
                        type="submit"
                        id="resend-btn"
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-[#0d5fe9] transition hover:text-[#084fc9] disabled:cursor-not-allowed disabled:text-slate-400"
                    >
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                            <path d="M3 3v5h5"/>
                            <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/>
                            <path d="M16 21h5v-5"/>
                        </svg>
                        <span id="resend-label">Resend Code</span>
                    </button>
                </form>

                <div class="mt-4">
                    <a href="{{ route('customer.register') }}" class="text-xs font-medium text-slate-400 hover:text-slate-600 hover:underline">
                        &larr; Mistyped number? Edit registration details
                    </a>
                </div>
            </div>
        </div>

        <p class="mt-6 text-center text-xs font-medium text-slate-500">
            &copy; {{ date('Y') }} QuickWash Express. All rights reserved.
        </p>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const resendBtn = document.getElementById('resend-btn');
        const resendLabel = document.getElementById('resend-label');
        const otpInput = document.getElementById('otp_code');

        if (otpInput) {
            otpInput.focus();
        }

        // 60-second cooldown timer for resend button to prevent abuse
        let cooldown = 60;
        const cooldownKey = 'quickwash_otp_cooldown_until';
        const storedUntil = localStorage.getItem(cooldownKey);
        const now = Math.floor(Date.now() / 1000);

        let remaining = 0;
        if (storedUntil && parseInt(storedUntil, 10) > now) {
            remaining = parseInt(storedUntil, 10) - now;
        } else {
            // Set 60 second timer upon arriving on page if just sent
            remaining = 60;
            localStorage.setItem(cooldownKey, (now + 60).toString());
        }

        function updateTimer() {
            if (remaining > 0) {
                resendBtn.disabled = true;
                resendLabel.textContent = `Resend Code in ${remaining}s`;
                remaining--;
                setTimeout(updateTimer, 1000);
            } else {
                resendBtn.disabled = false;
                resendLabel.textContent = 'Resend Code';
                localStorage.removeItem(cooldownKey);
            }
        }

        updateTimer();

        resendBtn.addEventListener('click', function () {
            localStorage.setItem(cooldownKey, (Math.floor(Date.now() / 1000) + 60).toString());
        });
    });
</script>
@endsection
