@extends('staff.layout')

@section('content')
@php($pageTitle = 'Receipt')
@if($errors->any())
    <div class="mx-auto mb-4 max-w-3xl rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
    </div>
@endif
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
        aside, nav, header, .navbar, .sidebar {
            display: none !important;
        }
        main {
            padding: 0 !important;
        }
        .print-button-container {
            display: none !important;
        }
        .mx-auto {
            margin: 0 !important;
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
    }
</style>

<div class="print-receipt receipt-card mx-auto max-w-3xl rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-inside list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header -->
    <div class="receipt-brand text-center">
        <h2 class="text-2xl font-bold text-slate-800">QuickWash Express</h2>
        <p class="mt-1 text-sm text-slate-400">Official Payment Receipt</p>
        <p class="mt-2 text-xs text-slate-400">{{ now()->format('M d, Y h:i A') }}</p>
    </div>

    <!-- Receipt Info -->
    <div class="receipt-section mb-6 space-y-3 text-sm">
        <div class="flex justify-between">
            <span class="text-slate-600">Receipt #:</span>
            <span class="font-semibold text-slate-800">{{ $booking->latestPayment?->payment_reference ?? '—' }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-600">Booking Ref:</span>
            <span class="font-semibold text-slate-800">{{ $booking->booking_reference }}</span>
        </div>
    </div>

    <!-- Customer & Service -->
    <div class="receipt-section mb-6 grid md:grid-cols-2 gap-6 text-sm">
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Customer</p>
            <p class="mt-2 font-semibold text-slate-800">{{ $booking->customer->full_name ?? '—' }}</p>
        </div>
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Service</p>
            <p class="mt-2 font-semibold text-slate-800">{{ $booking->service->service_name ?? '—' }}</p>
        </div>
    </div>

    <!-- Amount Section -->
    @if($booking->latestPayment)
        <div class="receipt-summary space-y-3 rounded-lg p-5 text-sm">
            <div class="flex justify-between">
                <span class="text-slate-600">Total Amount:</span>
                <span class="text-slate-800">₱{{ number_format($booking->total_amount, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-600">Amount Paid:</span>
                <span class="font-semibold text-emerald-600">₱{{ number_format($booking->latestPayment->amount, 2) }}</span>
            </div>
            @if($booking->latestPayment->cash_received !== null)
                <div class="flex justify-between">
                    <span class="text-slate-600">Cash Received:</span>
                    <span class="font-semibold">&#8369;{{ number_format($booking->latestPayment->cash_received, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Change:</span>
                    <span class="font-semibold text-emerald-600">&#8369;{{ number_format($booking->latestPayment->change_amount, 2) }}</span>
                </div>
            @endif
        </div>

        <div class="receipt-section mt-6 space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-slate-600">Payment Method:</span>
                <span class="font-semibold">{{ $booking->latestPayment->payment_method }}</span>
            </div>
        </div>
    @endif

    @if($booking->latestPayment && $booking->latestPayment->status === 'Pending')
        <div class="mt-8 bg-amber-50 border border-amber-200 rounded-lg p-5">
            @if($booking->latestPayment->payment_method === 'Cash on Delivery')
            <p class="text-amber-900 font-semibold mb-4">💵 Confirm Cash Payment</p>
            <form method="POST" action="{{ route('staff.payments.confirm', $booking->latestPayment->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Amount Received from Customer</label>
                    <input type="number" step="0.01" name="amount_received" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="0.00" value="{{ old('amount_received', $booking->latestPayment->amount) }}" required>
                    <p class="text-xs text-slate-600 mt-1">Customer should provide: ₱{{ number_format($booking->latestPayment->amount, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Notes (Optional)</label>
                    <textarea name="notes" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" rows="2" placeholder="Add notes about payment confirmation..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" name="action" value="confirm" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm text-white hover:bg-emerald-700 font-medium">Confirm & Generate Receipt</button>
                    <button type="submit" name="action" value="reject" class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700 font-medium">Reject Payment</button>
                </div>
            </form>
        @elseif($booking->latestPayment->payment_method === 'GCash')
            <p class="text-blue-900 font-semibold mb-4">Confirm GCash Payment</p>
            <div class="mb-4 grid gap-4 md:grid-cols-2">
                <div class="rounded-lg bg-white p-4 text-sm">
                    <p><strong>Sender:</strong> {{ $booking->latestPayment->gcash_sender_number }}</p>
                    <p class="mt-2"><strong>Reference:</strong> {{ $booking->latestPayment->gcash_reference }}</p>
                </div>
                @if($booking->latestPayment->proof_image)
                    <a href="{{ route('payments.proof', $booking->latestPayment) }}" target="_blank" rel="noopener">
                        <img src="{{ route('payments.proof', $booking->latestPayment) }}" alt="GCash receipt" class="max-h-64 w-full rounded-lg border border-slate-200 object-contain">
                    </a>
                @endif
            </div>
            <form method="POST" action="{{ route('staff.payments.confirm', $booking->latestPayment->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">GCash Transaction ID</label>
                    <input type="text" name="gcash_transaction_id" value="{{ old('gcash_transaction_id', $booking->latestPayment->gcash_reference) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Notes (Optional)</label>
                    <textarea name="notes" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" rows="2" placeholder="Add notes about payment confirmation..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" name="action" value="confirm" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm text-white hover:bg-emerald-700 font-medium">Confirm & Generate Receipt</button>
                    <button type="submit" name="action" value="reject" class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700 font-medium">Reject Payment</button>
                </div>
            </form>
            @endif
        </div>
    @endif

    <div class="mt-8 border-t border-slate-200 pt-6 text-center text-xs text-slate-500">
        <p class="font-semibold">Thank you for choosing QuickWash Express!</p>
        <div class="mt-4 print-button-container">
            <button type="button" onclick="window.print()" class="text-brand-600 hover:underline">Print Receipt</button>
        </div>
    </div>
</div>
@endsection
