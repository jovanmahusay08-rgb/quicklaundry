@extends('customer.layout')

@section('content')
@php($pageTitle = 'Booking Details')
<div class="py-2">
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Booking Details</h1>
                </div>
                <x-back-button :href="route('customer.bookings')">Back to bookings</x-back-button>
            </div>

            <div class="grid md:grid-cols-[1.2fr_0.8fr] gap-6 text-sm">
                <div class="rounded-xl bg-slate-50 p-5">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Booking Reference</p>
                    <p class="mt-2 font-semibold text-slate-800">{{ $booking->booking_reference }}</p>
                    <div class="mt-4 space-y-3">
                        <p><span class="font-semibold text-slate-700">Service:</span> {{ $booking->service->service_name ?? '—' }}</p>
                        <p><span class="font-semibold text-slate-700">Status:</span> {{ $booking->status }}</p>
                        <p><span class="font-semibold text-slate-700">Pickup:</span> {{ $booking->pickup_date?->format('M d, Y') ?? '—' }}{{ $booking->pickup_time ? ' at '.date('h:i A', strtotime($booking->pickup_time)) : '' }}</p>
                    </div>
                </div>
                <div class="rounded-xl bg-brand-50 p-5">
                    <p class="text-xs uppercase tracking-[0.2em] text-brand-700">Summary</p>
                    <div class="mt-4 space-y-3">
                        <p><span class="font-semibold text-slate-700">Additional Address Information:</span> {{ collect([$booking->delivery_address, $booking->delivery_purok, $booking->delivery_barangay, $booking->delivery_municipality])->filter()->join(', ') }}</p>
                        <p><span class="font-semibold text-slate-700">Phone:</span> {{ $booking->delivery_phone }}</p>
                        <p><span class="font-semibold text-slate-700">Total Amount:</span> ₱{{ number_format($booking->total_amount, 2) }}</p>
                        <p><span class="font-semibold text-slate-700">Payment:</span> {{ $booking->payment_status }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Payment</h2>
            @if($booking->payment_status !== 'Paid' && ($booking->payments->isEmpty() || $booking->latestPayment?->status === 'Failed'))
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-slate-50 rounded-xl p-5">
                        <p class="text-sm text-slate-600 mb-2">Total Amount Due</p>
                        <p class="text-3xl font-bold text-slate-800">₱{{ number_format($booking->total_amount, 2) }}</p>
                    </div>
                    <form id="paymentForm" method="POST" action="{{ route('customer.bookings.pay', $booking->id) }}" class="space-y-4">
                        @csrf
                        @if ($errors->any())
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <p>• {{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Payment Method</label>
                            <select name="payment_method" id="paymentMethod" class="w-full rounded-lg border border-slate-300 px-3 py-2 @error('payment_method') border-red-500 @enderror" required>
                                <option value="">Select payment method</option>
                                <option value="Cash on Delivery" {{ old('payment_method') == 'Cash on Delivery' ? 'selected' : '' }}>Cash on Delivery</option>
                                <option value="GCash" {{ old('payment_method') == 'GCash' ? 'selected' : '' }}>GCash</option>
                            </select>
                            @error('payment_method')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div id="paymentInfo" class="p-3 rounded-lg bg-slate-50 text-sm text-slate-600 hidden">
                            <p id="paymentInfoText"></p>
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-brand-600 px-4 py-2 text-white hover:bg-brand-700 transition disabled:opacity-50 disabled:cursor-not-allowed">Confirm Payment</button>
                    </form>
                    <script>
                        document.getElementById('paymentMethod').addEventListener('change', function() {
                            const infoDiv = document.getElementById('paymentInfo');
                            const infoText = document.getElementById('paymentInfoText');
                            
                            if (this.value === 'Cash on Delivery') {
                                infoText.textContent = 'Admin will verify and confirm your payment.';
                                infoDiv.classList.remove('hidden');
                            } else if (this.value === 'GCash') {
                                infoText.textContent = 'You will be redirected to GCash to complete your transaction.';
                                infoDiv.classList.remove('hidden');
                            } else {
                                infoDiv.classList.add('hidden');
                            }
                        });
                    </script>
                </div>
            @elseif($booking->payment_status === 'Paid')
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5">
                    <p class="text-emerald-700 font-semibold">✓ Payment Confirmed</p>
                    <p class="text-sm text-emerald-600 mt-2">Status: {{ $booking->payment_status }}</p>
                    <a href="{{ route('customer.bookings.receipt', $booking->id) }}" class="inline-block mt-3 text-sm text-brand-600 hover:underline">View Receipt</a>
                </div>
            @else
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                    <p class="font-semibold text-amber-800">Payment awaiting verification</p>
                    <p class="mt-2 text-sm text-amber-700">Method: {{ $booking->latestPayment?->payment_method ?? $booking->payment_method }}</p>
                    @if($booking->latestPayment?->payment_method === 'GCash')
                        <a href="{{ route('customer.payments.gcash', $booking->latestPayment) }}" class="mt-3 inline-block text-sm font-medium text-blue-600 hover:underline">
                            {{ $booking->latestPayment->submitted_at ? 'View GCash submission' : 'Complete GCash payment' }}
                        </a>
                    @elseif($booking->latestPayment?->payment_method === 'Cash on Delivery')
                        <a href="{{ route('customer.bookings.payment.resume', $booking) }}" class="mt-3 inline-block text-sm font-medium text-brand-600 hover:underline">View payment instructions</a>
                    @endif
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Order Tracking</h2>
            @forelse ($booking->tracking as $entry)
                <div class="border-l-2 border-brand-500 pl-4 py-2">
                    <p class="font-medium text-slate-800">{{ $entry->status }}</p>
                    <p class="text-sm text-slate-500">{{ $entry->timestamp->format('M d, Y h:i A') }}</p>
                    <p class="text-sm text-slate-600">{{ $entry->notes }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-400">No tracking updates yet.</p>
            @endforelse
        </div>
    </div>
</div>

<script>
function calculateChange() {
    const totalAmount = {{ $booking->total_amount }};
    const amountPaid = parseFloat(document.getElementById('amountPaid')?.value) || 0;
    const change = Math.max(0, amountPaid - totalAmount);
    
    document.getElementById('changeAmount').textContent = change.toFixed(2);
    
    // Disable submit if payment is less than total
    const submitBtn = document.querySelector('#paymentForm button[type="submit"]');
    if (document.getElementById('amountPaid') && amountPaid < totalAmount) {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

// Validate on form submit
document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
    const totalAmount = {{ $booking->total_amount }};
    const amountPaid = parseFloat(document.getElementById('amountPaid')?.value) || 0;
    
    if (document.getElementById('amountPaid') && amountPaid < totalAmount) {
        e.preventDefault();
        alert('Payment amount must be at least ₱' + totalAmount.toFixed(2));
    }
});
</script>
@endsection
