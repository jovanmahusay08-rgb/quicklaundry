@extends('admin.layouts.app')

@section('title', 'Payments')
@section('pageTitle', 'Payments')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif
    <form method="GET" class="bg-white rounded-xl border border-slate-200 p-4 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[220px]">
            <label class="block text-sm font-medium text-slate-600 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Payment ref, booking ref, or customer">
        </div>
        <div class="min-w-[220px]">
            <label class="block text-sm font-medium text-slate-600 mb-1">Status</label>
            <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                <option value="">All statuses</option>
                <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Failed" {{ request('status') === 'Failed' ? 'selected' : '' }}>Failed</option>
                <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        <button class="rounded-lg bg-brand-600 px-4 py-2 text-white">Filter</button>
        <div class="ml-auto flex items-center gap-2">
            <button type="button" id="select-all-payments" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-slate-700 hover:bg-slate-50">Select All</button>
            <button type="submit" form="payment-bulk-delete-form" id="delete-selected-payments" class="rounded-lg bg-rose-600 px-4 py-2 text-white hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-50" disabled>Delete Selected</button>
        </div>
    </form>
    <form id="payment-bulk-delete-form" method="POST" action="{{ route('admin.payments.destroy-selected') }}" onsubmit="return confirm('Delete all selected payments? This cannot be undone.');">
        @csrf
        @method('DELETE')
    </form>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-left"><span class="sr-only">Select</span></th>
                        <th class="px-5 py-3 text-left">Reference</th>
                        <th class="px-5 py-3 text-left">Customer</th>
                        <th class="px-5 py-3 text-left">Amount</th>
                        <th class="px-5 py-3 text-left">Method</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-left">Date</th>
                        <th class="px-5 py-3 text-left">Time</th>
                        <th class="px-5 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $payment)
                        <tr>
                            <td class="px-5 py-3">
                                <input type="checkbox" name="payment_ids[]" value="{{ $payment->id }}" form="payment-bulk-delete-form" class="payment-select rounded border-slate-300 text-brand-600 focus:ring-brand-500" aria-label="Select payment {{ $payment->payment_reference }}">
                            </td>
                            <td class="px-5 py-3 font-mono text-xs text-slate-700">{{ $payment->payment_reference ?? ($payment->booking->booking_reference ?? '—') }}</td>
                            <td class="px-5 py-3">
                                @if($payment->booking && $payment->booking->customer)
                                    <div class="text-slate-800 font-medium">{{ $payment->booking->customer->full_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $payment->booking->customer->email }}</div>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-5 py-3 font-semibold">
                                @if($payment->booking)
                                    <div class="text-slate-800">₱{{ number_format($payment->booking->total_amount, 2) }}</div>
                                @else
                                    ₱{{ number_format($payment->amount, 2) }}
                                @endif
                            </td>
                            <td class="px-5 py-3">{{ $payment->payment_method ?? '—' }}</td>
                            <td class="px-5 py-3">
                                @php
                                    $status = strtoupper($payment->status ?? '—');
                                    $badgeClass = $status === 'PAID' || $status === 'COMPLETED' ? 'bg-emerald-100 text-emerald-700' : ($status === 'PENDING' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700');
                                @endphp
                                <span class="rounded-full px-3 py-1 text-xs {{ $badgeClass }}">{{ $status }}</span>
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-500">{{ $payment->created_at?->format('M d, Y') }}</td>
                            <td class="px-5 py-3 text-sm text-slate-500">{{ $payment->created_at?->format('h:i A') }}</td>
                            <td class="px-5 py-3 text-sm">
                                <a href="{{ route('admin.payments.show', $payment->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded border border-slate-200 mr-2 hover:bg-slate-50" title="View">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.payments.show', [$payment->id, 'print' => 1]) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded border border-slate-200 hover:bg-slate-50" title="Print">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18H4a2 2 0 01-2-2V9a2 2 0 012-2h16a2 2 0 012 2v7a2 2 0 01-2 2h-2"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}" class="inline" onsubmit="return confirm('Delete payment {{ $payment->payment_reference }}? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-2 inline-flex h-8 w-8 items-center justify-center rounded border border-rose-200 text-rose-600 hover:bg-rose-50" title="Delete" aria-label="Delete payment">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M10 11v6M14 11v6M9 7l1-2h4l1 2M8 7l1 13h6l1-13"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-5 py-6 text-center text-slate-400">No payments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="{{ $payments->hasPages() ? 'flex justify-center' : '' }}">
        {{ $payments->links() }}
    </div>
</div>

<script>
    const paymentSelectAll = document.getElementById('select-all-payments');
    const paymentDeleteSelected = document.getElementById('delete-selected-payments');
    const paymentCheckboxes = [...document.querySelectorAll('.payment-select')];

    const updatePaymentSelection = () => {
        const selectedCount = paymentCheckboxes.filter((checkbox) => checkbox.checked).length;
        paymentDeleteSelected.disabled = selectedCount === 0;
        paymentSelectAll.textContent = selectedCount === paymentCheckboxes.length && selectedCount > 0 ? 'Clear Selection' : 'Select All';
    };

    paymentSelectAll.addEventListener('click', () => {
        const shouldSelect = paymentCheckboxes.some((checkbox) => !checkbox.checked);
        paymentCheckboxes.forEach((checkbox) => checkbox.checked = shouldSelect);
        updatePaymentSelection();
    });

    paymentCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', updatePaymentSelection));
</script>
@endsection
