@extends('customer.layout')

@section('content')
@php($pageTitle = 'Tracking')
<div class="py-2">
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Order Tracking</h1>
                </div>
                <x-back-button :href="route('customer.dashboard')">Back</x-back-button>
            </div>

            @forelse ($bookings as $booking)
                <div class="rounded-xl border border-slate-200 p-4 mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="font-semibold text-slate-800">{{ $booking->booking_reference }}</h2>
                        <span class="text-sm text-slate-500">{{ $booking->status }}</span>
                    </div>
                    <p class="text-sm text-slate-600">Service: {{ $booking->service->service_name ?? '—' }}</p>
                    @forelse ($booking->tracking as $entry)
                        <div class="mt-3 border-l-2 border-brand-500 pl-4">
                            <p class="text-sm font-medium text-slate-800">{{ $entry->status }}</p>
                            <p class="text-xs text-slate-500">{{ $entry->timestamp->format('M d, Y h:i A') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 mt-3">No tracking updates yet.</p>
                    @endforelse
                </div>
            @empty
                <p class="text-sm text-slate-400">No orders to track right now.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
