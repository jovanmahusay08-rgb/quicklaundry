@extends('customer.layout')

@section('content')
@php($pageTitle = 'Tracking')
<div class="py-1 sm:py-2">
    <div class="mx-auto max-w-5xl space-y-5 sm:space-y-6">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="bg-gradient-to-br from-brand-700 to-sky-500 p-5 text-white sm:p-7">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[.2em] text-sky-100">Live location</p>
                        <h1 class="mt-1 text-2xl font-bold">Order Tracking</h1>
                        <p class="mt-2 max-w-2xl text-sm text-sky-50">Share your current location while this page is open so QuickWash can pinpoint your pickup or delivery position.</p>
                    </div>
                    <button id="start-live-location" type="button" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-white px-5 text-sm font-bold text-brand-700 shadow-lg transition hover:bg-sky-50 disabled:cursor-not-allowed disabled:opacity-60">
                        <i class="fas fa-location-crosshairs"></i>
                        <span>Enable Live Location</span>
                    </button>
                </div>
                <p id="location-status" class="mt-4 rounded-lg bg-white/10 px-3 py-2 text-xs text-sky-50" role="status">Location sharing is off. Your location is only sent after you grant permission.</p>
            </div>
        </section>

        @forelse ($bookings as $booking)
            @php($isActive = !in_array($booking->status, ['Completed', 'Cancelled'], true))
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-2 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                    <div>
                        <h2 class="font-bold text-slate-800">{{ $booking->booking_reference }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $booking->service->service_name ?? 'Service' }}</p>
                    </div>
                    <span class="w-fit rounded-full px-3 py-1 text-xs font-bold {{ $isActive ? 'bg-sky-50 text-sky-700' : 'bg-slate-100 text-slate-600' }}">{{ $booking->status }}</span>
                </div>

                <div id="tracking-map-{{ $booking->id }}" class="h-72 w-full bg-slate-100 sm:h-80" aria-label="Map for booking {{ $booking->booking_reference }}"></div>

                <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Location</p>
                        <p id="location-time-{{ $booking->id }}" class="mt-1 text-sm text-slate-600">
                            @if($booking->location_updated_at)
                                Updated {{ $booking->location_updated_at->diffForHumans() }}
                            @elseif($isActive)
                                Waiting for live location permission
                            @else
                                Live tracking ended
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Order timeline</p>
                        @forelse ($booking->tracking->take(-3) as $entry)
                            <div class="mt-2 border-l-2 border-brand-500 pl-3">
                                <p class="text-sm font-semibold text-slate-700">{{ $entry->status }}</p>
                                <p class="text-xs text-slate-400">{{ $entry->timestamp->format('M d, Y h:i A') }}</p>
                            </div>
                        @empty
                            <p class="mt-1 text-sm text-slate-400">No order updates yet.</p>
                        @endforelse
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <i class="fas fa-map-location-dot text-3xl text-slate-300"></i>
                <p class="mt-3 text-sm text-slate-500">No orders to track right now.</p>
            </div>
        @endforelse
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    (() => {
        const bookings = [
            @foreach($bookings as $booking)
                @php($trackingActive = !in_array($booking->status, ['Completed', 'Cancelled'], true))
                @php($pickupCoordinates = $booking->location_latitude && $booking->location_longitude ? [(float) $booking->location_latitude, (float) $booking->location_longitude] : null)
                @php($liveCoordinates = $booking->live_latitude && $booking->live_longitude ? [(float) $booking->live_latitude, (float) $booking->live_longitude] : null)
                @php($locationAccuracy = $booking->location_accuracy !== null ? (float) $booking->location_accuracy : null)
                {
                    id: {{ $booking->id }},
                    active: @json($trackingActive),
                    endpoint: @json(route('customer.bookings.location.update', $booking)),
                    pickup: @json($pickupCoordinates),
                    live: @json($liveCoordinates),
                    accuracy: @json($locationAccuracy)
                },
            @endforeach
        ];
        const activeBookings = bookings.filter(booking => booking.active);
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const button = document.getElementById('start-live-location');
        const status = document.getElementById('location-status');
        const maps = new Map();
        let watchId = null;
        let lastSentAt = 0;

        bookings.forEach(booking => {
            const initial = booking.live || booking.pickup || [11.1547, 123.8056];
            const map = L.map(`tracking-map-${booking.id}`, { zoomControl: true }).setView(initial, booking.live || booking.pickup ? 16 : 11);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            const pickupMarker = booking.pickup ? L.marker(booking.pickup).addTo(map).bindPopup('Saved pickup location') : null;
            const liveMarker = booking.live ? L.circleMarker(booking.live, { radius: 9, color: '#ffffff', weight: 3, fillColor: '#0284c7', fillOpacity: 1 }).addTo(map).bindPopup('Current location') : null;
            const accuracyCircle = booking.live && booking.accuracy ? L.circle(booking.live, { radius: booking.accuracy, color: '#0284c7', weight: 1, fillOpacity: .08 }).addTo(map) : null;
            if (pickupMarker && liveMarker) map.fitBounds(L.latLngBounds([booking.pickup, booking.live]).pad(.25));
            maps.set(booking.id, { map, liveMarker, accuracyCircle });
            requestAnimationFrame(() => map.invalidateSize());
        });

        const updateMap = (booking, latitude, longitude, accuracy) => {
            const state = maps.get(booking.id);
            if (!state) return;
            const point = [latitude, longitude];
            if (!state.liveMarker) state.liveMarker = L.circleMarker(point, { radius: 9, color: '#ffffff', weight: 3, fillColor: '#0284c7', fillOpacity: 1 }).addTo(state.map).bindPopup('Current location');
            else state.liveMarker.setLatLng(point);
            if (state.accuracyCircle) state.accuracyCircle.setLatLng(point).setRadius(accuracy || 0);
            else if (accuracy) state.accuracyCircle = L.circle(point, { radius: accuracy, color: '#0284c7', weight: 1, fillOpacity: .08 }).addTo(state.map);
            state.map.setView(point, 17);
            document.getElementById(`location-time-${booking.id}`).textContent = 'Updated just now';
        };

        const sendLocation = async position => {
            const now = Date.now();
            if (now - lastSentAt < 10000) return;
            lastSentAt = now;
            const payload = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy
            };
            const results = await Promise.allSettled(activeBookings.map(async booking => {
                const response = await fetch(booking.endpoint, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify(payload)
                });
                if (!response.ok) throw new Error(`Location update failed (${response.status})`);
                updateMap(booking, payload.latitude, payload.longitude, payload.accuracy);
            }));
            const failed = results.filter(result => result.status === 'rejected').length;
            status.textContent = failed ? 'Some order locations could not be updated. Retrying automatically.' : `Live location active · accuracy about ${Math.round(payload.accuracy)} meters`;
        };

        button?.addEventListener('click', () => {
            if (!activeBookings.length) {
                status.textContent = 'There are no active orders that require location sharing.';
                return;
            }
            if (!navigator.geolocation) {
                status.textContent = 'Location services are not supported on this device.';
                return;
            }
            button.disabled = true;
            status.textContent = 'Requesting precise location permission…';
            watchId = navigator.geolocation.watchPosition(
                position => {
                    button.querySelector('span').textContent = 'Live Location Active';
                    sendLocation(position).catch(() => { status.textContent = 'Could not send the location. Check your connection and retry.'; });
                },
                error => {
                    button.disabled = false;
                    status.textContent = error.code === 1 ? 'Location permission was denied. Enable it in the app settings to use live tracking.' : 'Your current location is unavailable. Turn on GPS and try again.';
                },
                { enableHighAccuracy: true, maximumAge: 5000, timeout: 15000 }
            );
        });

        window.addEventListener('beforeunload', () => {
            if (watchId !== null) navigator.geolocation.clearWatch(watchId);
        });
    })();
</script>
@endsection
