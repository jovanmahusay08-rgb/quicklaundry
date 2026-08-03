@extends('customer.layout')

@section('content')
@php($pageTitle = 'GCash Payment')
<div class="py-2">
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-800">GCash Payment</h1>
            <x-back-button :href="route('customer.bookings.show', $booking)">Back</x-back-button>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            @if($errors->any())
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <div class="mb-6 space-y-2">
                <p><span class="font-semibold">Booking:</span> {{ $booking->booking_reference }}</p>
                <p><span class="font-semibold">Amount:</span> &#8369;{{ number_format($payment->amount, 2) }}</p>
                <p><span class="font-semibold">Payment reference:</span> {{ $payment->payment_reference }}</p>
            </div>

            <div class="mb-6 overflow-hidden rounded-2xl border border-blue-200 bg-blue-50">
                <div class="grid md:grid-cols-[minmax(0,280px)_1fr]">
                    <div class="bg-blue-600 p-5">
                        <img src="{{ asset(config('services.gcash.qr_image')) }}" alt="QuickWash GCash receiver QR code" class="mx-auto w-full max-w-[240px] rounded-xl bg-white object-contain shadow-lg">
                    </div>
                    <div class="flex flex-col justify-center p-6">
                        <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">Scan to pay</p>
                        <h2 class="mt-1 text-xl font-bold text-slate-900">Pay &#8369;{{ number_format($payment->amount, 2) }}</h2>
                        <dl class="mt-4 space-y-2 text-sm">
                            <div><dt class="inline text-slate-500">Receiver:</dt> <dd class="inline font-semibold text-slate-800">{{ config('services.gcash.account_name') }}</dd></div>
                            <div><dt class="inline text-slate-500">GCash number:</dt> <dd class="inline font-semibold text-slate-800">{{ config('services.gcash.number') }}</dd></div>
                        </dl>
                        <div class="mt-5 grid gap-2 sm:grid-cols-2">
                            <button type="button" id="open-gcash-app" class="cursor-pointer rounded-lg bg-blue-600 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Open GCash App</button>
                            <a href="{{ asset(config('services.gcash.qr_image')) }}" download="QuickWash-GCash-QR.jpg" class="rounded-lg border border-blue-300 bg-white px-4 py-2.5 text-center text-sm font-semibold text-blue-700 hover:bg-blue-100">Save QR Image</a>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">On another device, scan this QR. On the same phone, save it and use GCash's Upload QR option.</p>
                        <p id="gcash-launch-status" class="mt-1 text-xs text-blue-700"></p>
                    </div>
                </div>
            </div>

            <div class="mb-6 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                <p class="font-semibold">After paying</p>
                <p class="mt-1">Enter the transaction reference and upload the successful GCash receipt below for driver verification.</p>
            </div>

            @if($payment->submitted_at)
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                    <p class="font-semibold text-amber-800">Awaiting driver verification</p>
                    <p class="mt-2 text-sm text-amber-700">GCash reference: {{ $payment->gcash_reference }}</p>
                    <p class="text-sm text-amber-700">Submitted: {{ $payment->submitted_at->format('M d, Y h:i A') }}</p>
                </div>
            @else
                <form method="POST" action="{{ route('customer.payments.gcash.submit', $payment) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">GCash sender number</label>
                        <input name="gcash_sender_number" value="{{ old('gcash_sender_number') }}" inputmode="numeric" maxlength="11" placeholder="09XXXXXXXXX" class="w-full rounded-lg border-slate-300" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">GCash reference number</label>
                        <input id="gcash_reference" name="gcash_reference" value="{{ old('gcash_reference') }}" class="w-full rounded-lg border-slate-300" required>
                        <p id="receipt-scan-status" class="mt-1 text-xs text-slate-500">The reference will be detected after you attach the receipt.</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">GCash receipt screenshot</label>
                        <input id="proof_image" type="file" name="proof_image" accept="image/jpeg,image/png,image/webp" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2" required>
                        <p class="mt-1 text-xs text-slate-500">JPG, PNG, or WebP; maximum 5 MB.</p>
                    </div>
                    <button class="w-full rounded-lg bg-blue-600 px-4 py-3 font-semibold text-white hover:bg-blue-700">Submit GCash Payment</button>
                </form>
            @endif
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@7/dist/tesseract.min.js"></script>
<script>
    (() => {
        const openButton = document.getElementById('open-gcash-app');
        openButton?.addEventListener('click', () => {
            const isAndroid = /Android/i.test(navigator.userAgent);
            const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
            const launchStatus = document.getElementById('gcash-launch-status');

            if (isAndroid) {
                launchStatus.textContent = 'Opening GCash...';
                const fallback = encodeURIComponent('https://play.google.com/store/apps/details?id=com.globe.gcash.android');
                window.location.href = `intent://open#Intent;scheme=gcash;package=com.globe.gcash.android;S.browser_fallback_url=${fallback};end`;
            } else if (isIOS) {
                launchStatus.textContent = 'Opening GCash...';
                window.location.href = 'gcash://';
                window.setTimeout(() => {
                    if (!document.hidden) window.location.href = 'https://apps.apple.com/ph/app/gcash/id520020791';
                }, 1800);
            } else {
                launchStatus.textContent = 'GCash app launch is available on mobile. Opening the GCash website instead.';
                window.open('https://gcash.com/', '_blank', 'noopener');
            }
        });

        const receipt = document.getElementById('proof_image');
        const reference = document.getElementById('gcash_reference');
        const status = document.getElementById('receipt-scan-status');

        receipt?.addEventListener('change', async () => {
            const file = receipt.files?.[0];
            if (!file || !window.Tesseract) return;

            status.textContent = 'Reading receipt and finding the reference number...';
            status.className = 'mt-1 text-xs text-blue-600';

            try {
                const result = await Tesseract.recognize(file, 'eng', {
                    logger: progress => {
                        if (progress.status === 'recognizing text') {
                            status.textContent = `Reading receipt... ${Math.round(progress.progress * 100)}%`;
                        }
                    }
                });
                const text = result.data.text;
                const lines = text.split(/\r?\n/).filter(Boolean);
                const referenceLine = lines.find(line => /ref(?:erence)?(?:\s*(?:no|number|id))?/i.test(line));
                const exactReference = text.match(/(?:\d[\s-]*){13}/)?.[0]?.replace(/\D/g, '');
                const candidates = [exactReference, referenceLine, ...lines]
                    .filter(Boolean)
                    .map(line => line.replace(/\D/g, ''))
                    .filter(value => value.length === 13);

                if (candidates.length) {
                    reference.value = candidates[0];
                    reference.dispatchEvent(new Event('input', { bubbles: true }));
                    status.textContent = 'Reference number detected. Please verify it before submitting.';
                    status.className = 'mt-1 text-xs text-emerald-600';
                } else {
                    status.textContent = 'Reference number was not clear. Please enter it manually.';
                    status.className = 'mt-1 text-xs text-amber-600';
                }
            } catch (error) {
                status.textContent = 'Receipt could not be read. Please enter the reference manually.';
                status.className = 'mt-1 text-xs text-amber-600';
            }
        });
    })();
</script>
@endsection
