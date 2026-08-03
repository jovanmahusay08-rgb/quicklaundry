@extends('admin.layouts.app')

@section('title', 'Booking Details')
@section('pageTitle', 'Booking Details')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-6 space-y-6">
        <div>
            <p class="text-sm text-slate-500">Reference</p>
            <p class="text-xl font-semibold text-slate-900">{{ $booking->booking_reference }}</p>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div class="rounded-lg border border-slate-200 p-4">
                <p class="text-sm text-slate-500">Customer</p>
                <p class="font-medium text-slate-800">{{ $booking->customer->full_name ?? '—' }}</p>
                <p class="text-sm text-slate-500">{{ $booking->customer->email ?? '—' }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 p-4">
                <p class="text-sm text-slate-500">Service</p>
                <p class="font-medium text-slate-800">{{ $booking->service->service_name ?? '—' }}</p>
                <p class="text-sm text-slate-500">{{ $booking->quantity_kg }} kg</p>
            </div>
        </div>
        <div class="rounded-lg border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Status</p>
            <p class="font-semibold text-slate-800">{{ $booking->status }}</p>
            @php
                $paidAmount = $booking->payments()->whereIn('status', ['Paid', 'Completed'])->sum('amount');
                $isPaid = ($booking->payment_status === 'Paid') || ($paidAmount >= $booking->total_amount && $paidAmount > 0);
            @endphp
            <p class="text-sm text-slate-500 mt-2">Payment:
                @if($isPaid)
                    <span class="text-emerald-700 font-semibold">Paid</span>
                    <span class="text-sm text-emerald-600 mt-2">Amount Paid: ₱{{ number_format($paidAmount, 2) }}</span>
                @else
                    <span class="text-rose-600 font-semibold">Unpaid</span>
                @endif
            </p>
        </div>
        <div class="rounded-lg border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Tracking History</p>
            <ul class="mt-3 space-y-2 text-sm">
                @forelse ($booking->tracking as $entry)
                    <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2"><span>{{ $entry->status }}</span><span class="text-slate-500">{{ $entry->timestamp?->format('M d, Y H:i') }}</span></li>
                @empty
                    <li class="text-slate-400">No tracking events yet.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h2 class="font-semibold text-slate-800 mb-4">Billing</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">Subtotal</span><span>₱{{ number_format($booking->subtotal, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Discount</span><span>-₱{{ number_format($booking->discount, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Rush Fee</span><span>₱{{ number_format($booking->rush_fee, 2) }}</span></div>
                <div class="flex justify-between font-semibold text-slate-900"><span>Total</span><span>₱{{ number_format($booking->total_amount, 2) }}</span></div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h2 class="font-semibold text-slate-800 mb-4">Assigned Staff</h2>
            <p class="text-sm text-slate-600">{{ $booking->assignedStaff->full_name ?? 'Not assigned yet' }}</p>
        </div>
    </div>
</div>
@endsection
