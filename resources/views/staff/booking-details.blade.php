@extends('staff.layout')

@section('content')
@php($pageTitle = 'Booking Details')
<div class="space-y-6">
    @if (session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ session('error') }}</div>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Reference</p>
                <h2 class="text-xl font-semibold text-slate-800">{{ $booking->booking_reference }}</h2>
            </div>
            <div class="flex gap-3">
                @if($staff->role === 'Driver' && $booking->assigned_driver_id === $staff->id && $booking->payment_status !== 'Paid' && ($booking->latestPayment?->payment_method !== 'GCash' || $booking->latestPayment?->submitted_at))
                    <a href="{{ route('staff.receipt', $booking) }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Payment</a>
                @endif
                @if($booking->status !== 'Completed')
                    <a href="{{ route('staff.bookings.status', $booking) }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Update Status</a>
                @endif
            </div>
        </div>

        <div class="grid gap-6 text-sm text-slate-700 md:grid-cols-2">
            <div>
                <p class="font-semibold text-slate-800">Customer</p>
                <p>{{ $booking->customer->full_name ?? '—' }}</p>
                <p class="mt-2 font-semibold text-slate-800">Service</p>
                <p>{{ $booking->service->service_name ?? '—' }}</p>
                <p class="mt-2 font-semibold text-slate-800">Status</p>
                <p>{{ $booking->status }}</p>
                @if($booking->assignedDriver)
                    <p class="mt-2 font-semibold text-slate-800">Delivery Driver</p>
                    <p>{{ $booking->assignedDriver->full_name }}</p>
                @endif
            </div>
            <div>
                <p class="font-semibold text-slate-800">Delivery</p>
                <p>{{ collect([$booking->delivery_address, $booking->delivery_purok, $booking->delivery_barangay, $booking->delivery_municipality])->filter()->join(', ') }}</p>
                <p class="mt-2 font-semibold text-slate-800">Phone</p>
                <p>{{ $booking->delivery_phone }}</p>
                <p class="mt-2 font-semibold text-slate-800">Total</p>
                <p>₱{{ number_format($booking->total_amount, 2) }}</p>
            </div>
        </div>
    </div>

    @if($booking->location_latitude !== null && $booking->location_longitude !== null)
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-3 flex items-center justify-between gap-3">
                <div>
                    <h3 class="font-semibold text-slate-800">Customer Pickup Location</h3>
                    <p class="text-xs text-slate-500">Pinned by the customer during booking.</p>
                </div>
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $booking->location_latitude }},{{ $booking->location_longitude }}" target="_blank" rel="noopener" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">
                    Get Directions
                </a>
            </div>
            <div id="driver-location-map" class="h-96 w-full rounded-xl border border-slate-300"></div>
        </div>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            (() => {
                const point = [{{ (float) $booking->location_latitude }}, {{ (float) $booking->location_longitude }}];
                const map = L.map('driver-location-map').setView(point, 17);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19, attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                L.marker(point).addTo(map).bindPopup(@json($booking->customer->full_name ?? 'Customer pickup location')).openPopup();
            })();
        </script>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="mb-3 font-semibold text-slate-800">Tracking</h3>
        @forelse ($booking->tracking as $entry)
            <div class="border-l-2 border-brand-500 py-2 pl-4">
                <p class="text-sm font-medium text-slate-800">{{ $entry->status }}</p>
                <p class="text-xs text-slate-500">{{ $entry->timestamp?->format('M d, Y h:i A') }}</p>
            </div>
        @empty
            <p class="text-sm text-slate-400">No tracking updates yet.</p>
        @endforelse
    </div>
</div>
@endsection
