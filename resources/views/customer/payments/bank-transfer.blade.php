@extends('customer.layout')

@section('content')
@php($pageTitle = 'Bank Transfer Payment')
<div class="py-2">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Bank Transfer Payment</h1>
            </div>
            <x-back-button :href="route('customer.bookings.show', $booking->id)">Back</x-back-button>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-slate-800 mb-4">Payment Details</h2>
                <div class="space-y-3">
                    <p><span class="font-semibold text-slate-700">Booking Reference:</span> {{ $booking->booking_reference }}</p>
                    <p><span class="font-semibold text-slate-700">Amount Due:</span> <span class="text-2xl font-bold text-emerald-600">₱{{ number_format($payment->amount, 2) }}</span></p>
                    <p><span class="font-semibold text-slate-700">Payment Method:</span> Bank Transfer</p>
                    <p><span class="font-semibold text-slate-700">Status:</span> <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded text-sm">{{ $payment->status }}</span></p>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                <p class="text-amber-900 text-sm font-semibold">⚠️ Bank Transfer Instructions:</p>
                <div class="text-amber-900 text-sm mt-3 space-y-3">
                    <div>
                        <p class="font-semibold">Bank Details:</p>
                        <p>Bank Name: BDO (Banco de Oro)</p>
                        <p>Account Name: QuickWash Express</p>
                        <p>Account Number: 123456789</p>
                        <p>Branch Code: BNORPHPH</p>
                    </div>
                    <div>
                        <p class="font-semibold">Transfer Steps:</p>
                        <ol class="list-decimal ml-4 mt-1 space-y-1">
                            <li>Use the account details above</li>
                            <li>Enter amount: ₱{{ number_format($payment->amount, 2) }}</li>
                            <li>Use reference: {{ $payment->payment_reference }}</li>
                            <li>Complete the transfer</li>
                            <li>Admin will verify within 24 hours</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <x-back-button :href="route('customer.bookings.show', $booking->id)" variant="primary">
                    Back to Booking
                </x-back-button>
            </div>

            <div class="mt-6 p-4 bg-slate-50 rounded-lg">
                <p class="text-xs text-slate-500 font-semibold">Important:</p>
                <ul class="text-xs text-slate-500 mt-2 space-y-1">
                    <li>• Please use the payment reference as description for identification</li>
                    <li>• Transfer fees are on customer's account</li>
                    <li>• Admin will confirm receipt within 24 hours</li>
                    <li>• Payment Reference: <span class="font-mono">{{ $payment->payment_reference }}</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
