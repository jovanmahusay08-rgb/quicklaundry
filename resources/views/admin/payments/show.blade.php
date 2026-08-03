@extends('admin.layouts.app')

@section('title', 'Payment Receipt')
@section('pageTitle', 'Payment Receipt')

@section('content')
<style>
    .receipt-card { overflow: hidden; border-color: #dbe4ee; }
    .receipt-brand { margin: -2rem -2rem 2rem; padding: 2rem; background: linear-gradient(135deg, #075985, #0284c7); color: white; }
    .receipt-brand h2, .receipt-brand p { color: white; }
    .receipt-section { border: 1px solid #e2e8f0; border-radius: .75rem; padding: 1.25rem; background: #fff; }
    .receipt-summary { border: 1px solid #bae6fd; background: #f0f9ff; }
    @media print {
        body {
            margin: 0;
            padding: 0;
            background: white;
        }
        aside, nav, header, .navbar, .sidebar, [class*="sidebar"], [class*="navbar"] {
            display: none !important;
        }
        main {
            padding: 0 !important;
        }
        .max-w-3xl {
            max-width: 100% !important;
            margin: 0 !important;
        }
        .mx-auto {
            margin: 0 !important;
        }
        .header-section {
            display: none !important;
        }
        .form-section {
            display: none !important;
        }
        .action-buttons {
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
        button, input, textarea, select {
            display: none !important;
        }
        a {
            color: #000 !important;
            text-decoration: none !important;
        }
    }
</style>

<div class="max-w-3xl mx-auto">
    @if(session('info'))
        <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">{{ session('info') }}</div>
    @endif
    <div class="print-receipt receipt-card rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
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
                <span class="font-semibold text-slate-800">{{ $payment->booking?->booking_reference ?? 'Deleted booking' }}</span>
            </div>
        </div>

        <!-- Customer & Service Info -->
        <div class="receipt-section grid md:grid-cols-2 gap-6 mb-6 text-sm">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Customer</p>
                <p class="mt-2 font-semibold text-slate-800">{{ $payment->booking?->customer?->full_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Service</p>
                <p class="mt-2 font-semibold text-slate-800">{{ $payment->booking?->service?->service_name ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Amount Section -->
        <div class="receipt-summary mb-6 rounded-lg p-5 space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-slate-600">Total Amount:</span>
                <span class="text-slate-800">&#8369;{{ number_format($payment->booking?->total_amount ?? $payment->amount, 2) }}</span>
            </div>
            @if(($payment->booking?->discount ?? 0) > 0)
                <div class="flex justify-between text-emerald-600">
                    <span>Discount:</span>
                    <span>-&#8369;{{ number_format($payment->booking?->discount, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between">
                <span class="text-slate-600">Booking Ref:</span>
                <span class="font-semibold">{{ $payment->booking?->booking_reference ?? 'Deleted booking' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-600">Payment Method:</span>
                <span class="font-semibold">{{ $payment->payment_method ?? '—' }}</span>
            </div>
            <div class="flex justify-between border-t border-slate-200 pt-3 font-semibold">
                <span class="text-slate-600">Amount Paid:</span>
                <span class="font-semibold text-emerald-600">₱{{ number_format($payment->amount, 2) }}</span>
            </div>
            @if($payment->cash_received !== null)
                <div class="flex justify-between">
                    <span class="text-slate-600">Cash Received:</span>
                    <span class="font-semibold">&#8369;{{ number_format($payment->cash_received, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Change:</span>
                    <span class="font-semibold text-emerald-600">&#8369;{{ number_format($payment->change_amount, 2) }}</span>
                </div>
            @endif
        </div>

        @if($payment->status === 'Pending')
            <div class="mb-6 bg-amber-50 border border-amber-200 rounded-lg p-4 form-section">
                @if($payment->payment_method === 'Cash on Delivery')
                    <p class="text-amber-900 font-semibold mb-4">💵 Confirm Cash on Delivery Payment</p>
                    <p class="text-amber-900 text-sm mb-4">Customer should provide: ₱{{ number_format($payment->amount, 2) }}</p>
                    <form method="POST" action="{{ route('admin.payments.confirm', $payment->id) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Amount Received from Customer</label>
                            <input type="number" step="0.01" name="amount_received" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="0.00" value="{{ old('amount_received', $payment->amount) }}" required>
                            <p class="text-xs text-slate-600 mt-1">Enter the exact amount the customer provided</p>
                        </div>
                        <textarea name="notes" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" rows="3" placeholder="Optional: Add notes about payment confirmation..."></textarea>
                        <div class="flex gap-3">
                            <button type="submit" name="action" value="confirm" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm text-white hover:bg-emerald-700">Confirm & Generate Receipt</button>
                            <button type="submit" name="action" value="reject" class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">Reject Payment</button>
                        </div>
                    </form>
                @elseif($payment->payment_method === 'GCash')
                    <p class="text-blue-900 font-semibold mb-3">📱 GCash Payment Status</p>
                    <p class="text-blue-900 text-sm mb-4">Waiting for customer to complete GCash transaction. GCash will notify when payment is received.</p>
                    <form method="POST" action="{{ route('admin.payments.confirm', $payment->id) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">GCash Transaction ID (if received)</label>
                            <input type="text" name="gcash_transaction_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="e.g., GCH-123456789">
                        </div>
                        <textarea name="notes" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" rows="3" placeholder="Optional: Add notes about payment confirmation..." ></textarea>
                        <div class="flex gap-3">
                            <button type="submit" name="action" value="confirm" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm text-white hover:bg-emerald-700">Confirm GCash Payment</button>
                            <button type="submit" name="action" value="reject" class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">Reject Payment</button>
                        </div>
                    </form>
                @endif
            </div>
        @endif

        <div class="border-t border-slate-200 pt-6 text-center text-xs text-slate-500">
            <p class="font-semibold">Thank you for choosing QuickWash Express!</p>
            <div class="mt-4 flex justify-center gap-3 action-buttons">
                <x-back-button :href="url()->previous() ?? route('admin.payments')">Back</x-back-button>
                <button type="button" onclick="window.print()" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm text-white">Print Receipt</button>
            </div>
        </div>
    </div>
</div>

@if(request()->has('print'))
    <script>window.print();</script>
@endif

@endsection
