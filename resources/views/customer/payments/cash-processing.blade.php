@extends('customer.layout')

@section('content')
@php($pageTitle = 'Payment Processing')
<div class="py-2">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Payment Processing</h1>
            </div>
            <x-back-button :href="route('customer.bookings.show', $booking->id)">Back</x-back-button>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <div class="mb-6 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-slate-800 mb-2">Payment Being Processed</h2>
                <p class="text-slate-600">Your cash payment is being verified and processed by our admin team</p>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-blue-900 font-semibold mb-3">📋 Payment Details:</p>
                <div class="space-y-2 text-sm text-blue-900">
                    <p><span class="font-medium">Booking Reference:</span> {{ $booking->booking_reference }}</p>
                    <p><span class="font-medium">Amount to Pay:</span> ₱{{ number_format($payment->amount, 2) }}</p>
                    <p><span class="font-medium">Payment Method:</span> Cash on Delivery</p>
                    <p><span class="font-medium">Payment Reference:</span> {{ $payment->payment_reference }}</p>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                <p class="text-amber-900 font-semibold mb-3">⏳ What happens next:</p>
                <ol class="text-amber-900 text-sm space-y-2 ml-4 list-decimal">
                    <li>Our admin team will receive your payment request</li>
                    <li>Admin will verify the cash amount you provided</li>
                    <li>A receipt will be automatically generated for you</li>
                    <li>You'll receive a notification once verified (within 24 hours)</li>
                </ol>
            </div>

            <div class="bg-slate-50 rounded-lg p-4 mb-6">
                <p class="text-slate-700 font-semibold mb-2">💡 Important:</p>
                <ul class="text-sm text-slate-600 space-y-1">
                    <li>• Make sure to provide the exact amount: ₱{{ number_format($payment->amount, 2) }}</li>
                    <li>• Keep this payment reference safe: <span class="font-mono text-slate-800">{{ $payment->payment_reference }}</span></li>
                    <li>• A receipt will be generated for both you and admin</li>
                </ul>
            </div>

            <div class="space-y-3">
                <x-back-button :href="route('customer.bookings.show', $booking->id)" variant="primary">
                    Back to Booking Details
                </x-back-button>
            </div>
        </div>
    </div>
</div>
@endsection
