@extends('customer.layout')

@section('content')
@php($pageTitle = 'Payment Receipt')
<style>
    .receipt-card { overflow: hidden; border-color: #dbe4ee; }
    .receipt-brand { margin: -2rem -2rem 2rem; padding: 2rem; background: linear-gradient(135deg, #075985, #0284c7); color: white; }
    .receipt-brand h2, .receipt-brand p { color: white; }
    .receipt-section { border: 1px solid #e2e8f0; border-radius: .75rem; padding: 1.25rem; background: #fff; }
    .receipt-summary { border: 1px solid #bae6fd; background: #f0f9ff; }
    .receipt-label { color: #64748b; font-size: .75rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
    @media print {
        body {
            margin: 0;
            padding: 0;
            background: white;
        }
        aside, nav, header, .navbar, .sidebar {
            display: none !important;
        }
        main {
            padding: 0 !important;
        }
        .py-2 {
            padding: 0 !important;
        }
        .max-w-3xl {
            max-width: 100% !important;
            margin: 0 !important;
        }
        .mx-auto {
            margin: 0 !important;
        }
        .mb-6:first-child,
        .header-section {
            display: none !important;
        }
        .print-button-container {
            display: none !important;
        }
        .rounded-2xl {
            box-shadow: none !important;
            border: 1px solid #e2e8f0 !important;
            page-break-inside: avoid;
        }
        .receipt-brand { margin: -2rem -2rem 2rem !important; background: #075985 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        * {
            box-shadow: none !important;
        }
        button {
            display: none !important;
        }
        a {
            color: #000 !important;
            text-decoration: none !important;
        }
    }
</style>

<div class="py-2">
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center justify-between header-section">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Payment Receipt</h1>
            </div>
            <x-back-button :href="route('customer.bookings.show', $booking->id)">Back to booking</x-back-button>
        </div>

        <div class="print-receipt receipt-card rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <!-- Header -->
            <div class="receipt-brand text-center">
                <h2 class="text-2xl font-bold text-slate-800">QuickWash Express</h2>
                <p class="mt-1 text-sm text-slate-400">Official Payment Receipt</p>
                <p class="mt-2 text-xs text-slate-400">{{ $payment->created_at?->format('M d, Y h:i A') ?? now()->format('M d, Y h:i A') }}</p>
            </div>

            <!-- Receipt Info -->
            <div class="receipt-section mb-6 space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-600">Receipt #:</span>
                    <span class="font-semibold text-slate-800">{{ $payment->payment_reference }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Booking Ref:</span>
                    <span class="font-semibold text-slate-800">{{ $booking->booking_reference }}</span>
                </div>
            </div>

            <!-- Customer & Service Info -->
            <div class="receipt-section mb-6 grid md:grid-cols-2 gap-6 text-sm">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Customer</p>
                    <p class="mt-2 font-semibold text-slate-800">{{ $customer->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Service</p>
                    <p class="mt-2 font-semibold text-slate-800">{{ $booking->service->service_name ?? '—' }}</p>
                </div>
            </div>

            <!-- Amount Section -->
            <div class="receipt-summary mb-6 rounded-lg p-5">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Total Amount:</span>
                        <span class="text-slate-800">₱{{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                    @if($booking->discount > 0)
                        <div class="flex justify-between text-emerald-600">
                            <span>Discount:</span>
                            <span>-₱{{ number_format($booking->discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between border-t border-slate-200 pt-3 font-semibold">
                        <span>Amount Paid:</span>
                        <span class="text-emerald-600">₱{{ number_format($payment->amount, 2) }}</span>
                    </div>
                    @if($payment->cash_received !== null)
                        <div class="flex justify-between">
                            <span>Cash Received:</span>
                            <span>&#8369;{{ number_format($payment->cash_received, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-700">
                            <span>Change:</span>
                            <span class="font-bold">₱{{ number_format($change, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Info -->
            <div class="receipt-section mb-8 space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-600">Payment Method:</span>
                    <span class="font-semibold">{{ $payment->payment_method }}</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-slate-200 pt-6 text-center text-xs text-slate-500">
                <p class="font-semibold">Thank you for choosing QuickWash Express!</p>
                <div class="mt-4 print-button-container">
                    <button onclick="window.print()" class="text-brand-600 hover:underline">Print Receipt</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
