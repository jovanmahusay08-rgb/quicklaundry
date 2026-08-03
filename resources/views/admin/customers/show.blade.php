@extends('admin.layouts.app')

@section('title', 'Customer Details')
@section('pageTitle', 'Customer Details')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Profile</h2>
        <div class="space-y-2 text-sm">
            <div><span class="text-slate-500">Name</span><p class="font-medium text-slate-800">{{ $customer->full_name }}</p></div>
            <div><span class="text-slate-500">Email</span><p class="font-medium text-slate-800">{{ $customer->email }}</p></div>
            <div><span class="text-slate-500">Phone</span><p class="font-medium text-slate-800">{{ $customer->phone }}</p></div>
            <div><span class="text-slate-500">Address</span><p class="font-medium text-slate-800">{{ $customer->address }}</p></div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Loyalty Points</h2>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-slate-500">Total</span><span class="font-medium text-slate-800">{{ $loyalty->total_points ?? 0 }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Available</span><span class="font-medium text-slate-800">{{ $loyalty->available_points ?? 0 }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Redeemed</span><span class="font-medium text-slate-800">{{ $loyalty->redeemed_points ?? 0 }}</span></div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Account Summary</h2>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-slate-500">Bookings</span><span class="font-medium text-slate-800">{{ $customer->bookings()->count() }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Total Spent</span><span class="font-medium text-slate-800">₱{{ number_format($customer->total_spent, 2) }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Status</span><span class="font-medium text-slate-800">{{ $customer->is_active ? 'Active' : 'Inactive' }}</span></div>
        </div>
    </div>
</div>

<div class="mt-6 grid lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Recent Bookings</h2>
        <ul class="space-y-2 text-sm">
            @forelse ($recentBookings as $booking)
                <li class="flex justify-between rounded-lg bg-slate-50 px-3 py-2"><span>{{ $booking->booking_reference }}</span><span class="text-slate-500">{{ $booking->service->service_name ?? '—' }}</span></li>
            @empty
                <li class="text-slate-400">No bookings yet.</li>
            @endforelse
        </ul>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Recent Payments</h2>
        <ul class="space-y-2 text-sm">
            @forelse ($payments as $payment)
                <li class="flex justify-between rounded-lg bg-slate-50 px-3 py-2"><span>{{ $payment->booking->booking_reference ?? '—' }}</span><span class="text-slate-500">₱{{ number_format($payment->amount, 2) }}</span></li>
            @empty
                <li class="text-slate-400">No payments yet.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
