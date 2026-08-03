@extends('customer.layout')

@section('content')
@php($pageTitle = 'Create Booking')
<div class="py-2">
    <div class="max-w-3xl mx-auto">
            <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Create Booking</h1>
            </div>
            <x-back-button :href="route('customer.bookings')">Back</x-back-button>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <p class="mb-6 text-sm text-slate-500">Fill in your request details and we’ll arrange pickup and delivery for you.</p>
            <form method="POST" action="{{ route('customer.bookings.store') }}" class="space-y-4">
                @csrf
                @if ($errors->any())
                    <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                        @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Service</label>
                    <select name="service_id" class="w-full rounded-lg border-slate-300" required>
                        <option value="">Select a service</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}">{{ $service->service_name }} — ₱{{ number_format($service->base_price, 2) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Weight (kg)</label>
                    <input id="quantity_kg" type="number" step="0.1" min="1" max="9" name="quantity_kg" value="{{ old('quantity_kg') }}" class="w-full rounded-lg border-slate-300" required>
                    <p class="mt-1 text-xs text-slate-500">1–7 kg: fixed ₱200.00 · Above 7–9 kg: fixed ₱400.00 · Maximum accepted weight: 9 kg.</p>
                    <div id="weight-price-preview" class="mt-2 hidden rounded-lg border px-3 py-2 text-sm" role="status"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Pickup Date</label>
                    <input type="date" name="pickup_date" value="{{ old('pickup_date') }}" min="{{ now()->toDateString() }}" class="w-full rounded-lg border-slate-300" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Pickup Time</label>
                    <input type="time" name="pickup_time" value="{{ old('pickup_time') }}" class="w-full rounded-lg border-slate-300" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Additional Address Information</label>
                    <textarea name="delivery_address" rows="2" class="w-full rounded-lg border-slate-300" required>{{ old('delivery_address') }}</textarea>
                </div>
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Municipality</label>
                        <select id="delivery_municipality" name="delivery_municipality" class="w-full rounded-lg border-slate-300" required><option value="">Select municipality</option></select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Barangay</label>
                        <select id="delivery_barangay" name="delivery_barangay" class="w-full rounded-lg border-slate-300" required disabled><option value="">Select barangay</option></select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Purok</label>
                        <select id="delivery_purok" name="delivery_purok" class="w-full rounded-lg border-slate-300" required disabled><option value="">Select purok</option></select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                    <input type="text" name="delivery_phone" value="{{ old('delivery_phone') }}" inputmode="numeric" pattern="[0-9]{11}" maxlength="11" class="w-full rounded-lg border-slate-300" placeholder="09123456789" required>
                </div>
                <div>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Pin Pickup Location</label>
                            <p class="text-xs text-slate-500">The map follows your municipality, barangay, and purok. Click to place the pin, then drag and drop it onto the exact pickup address.</p>
                        </div>
                        <button type="button" id="use-current-location" class="rounded-lg border border-slate-300 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50">Use my location</button>
                    </div>
                    <div class="relative isolate h-96 w-full overflow-hidden rounded-xl border border-slate-300 bg-slate-100 shadow-inner">
                        <div id="booking-location-map" class="absolute inset-0 z-0 h-full w-full"></div>
                        <div id="map-search-panel" class="absolute left-3 right-3 top-3 z-[1000]">
                            <div class="flex overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black/10">
                                <input id="map-search" type="search" class="min-w-0 flex-1 border-0 px-4 py-3 text-sm focus:ring-0" placeholder="Search street, landmark, or address">
                                <button id="map-search-button" type="button" class="bg-brand-600 px-4 text-white hover:bg-brand-700" aria-label="Search map"><i class="fas fa-search"></i></button>
                            </div>
                            <div id="map-search-results" class="mt-1 hidden max-h-44 overflow-y-auto rounded-lg bg-white shadow-lg ring-1 ring-black/10"></div>
                        </div>
                    </div>
                    <input type="hidden" id="location_latitude" name="location_latitude" value="{{ old('location_latitude') }}" required>
                    <input type="hidden" id="location_longitude" name="location_longitude" value="{{ old('location_longitude') }}" required>
                    <input type="hidden" id="location_confirmed" name="location_confirmed" value="{{ old('location_confirmed') }}">
                    <div class="mt-3 flex flex-col gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Selected pickup point</p>
                            <p id="selected-coordinates" class="mt-1 text-sm text-slate-600">No location selected yet.</p>
                        </div>
                        <button type="button" id="confirm-map-location" disabled class="rounded-lg bg-orange-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-orange-600 disabled:cursor-not-allowed disabled:opacity-40">Confirm Location</button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Special Instructions</label>
                    <textarea name="special_instructions" rows="3" class="w-full rounded-lg border-slate-300"></textarea>
                </div>
                <button id="submit-booking" type="submit" class="inline-flex rounded-lg bg-brand-600 px-4 py-2 text-white hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-50">Submit Booking</button>
            </form>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    (() => {
        const weight = document.getElementById('quantity_kg');
        const preview = document.getElementById('weight-price-preview');
        const submit = document.getElementById('submit-booking');

        const updatePrice = () => {
            const kilograms = Number.parseFloat(weight.value);
            preview.classList.toggle('hidden', !weight.value);
            preview.className = 'mt-2 rounded-lg border px-3 py-2 text-sm';

            if (!weight.value) return;
            if (kilograms > 9) {
                preview.textContent = 'QuickWash cannot accept loads above 9 kg.';
                preview.classList.add('border-red-200', 'bg-red-50', 'text-red-700');
                submit.disabled = true;
            } else if (kilograms >= 1) {
                const price = kilograms <= 7 ? 200 : 400;
                preview.textContent = `Fixed booking price: ₱${price.toFixed(2)}`;
                preview.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-700');
                submit.disabled = false;
            } else {
                preview.textContent = 'Weight must be at least 1 kg.';
                preview.classList.add('border-red-200', 'bg-red-50', 'text-red-700');
                submit.disabled = true;
            }
        };

        weight.addEventListener('input', updatePrice);
        updatePrice();
    })();

    (() => {
        const locations = @json($locations);
        const municipality = document.getElementById('delivery_municipality');
        const barangay = document.getElementById('delivery_barangay');
        const purok = document.getElementById('delivery_purok');
        const previous = @json([old('delivery_municipality'), old('delivery_barangay'), old('delivery_purok')]);
        const fill = (select, values, label, selected = '') => {
            select.innerHTML = `<option value="">${label}</option>`;
            values.forEach(value => select.add(new Option(value, value, false, value === selected)));
            select.disabled = values.length === 0;
        };
        const loadPuroks = (selected = '') => fill(purok, locations[municipality.value]?.[barangay.value] ?? [], 'Select purok', selected);
        const loadBarangays = (selectedBarangay = '', selectedPurok = '') => {
            fill(barangay, Object.keys(locations[municipality.value] ?? {}), 'Select barangay', selectedBarangay);
            loadPuroks(selectedPurok);
        };
        fill(municipality, Object.keys(locations), 'Select municipality', previous[0]);
        loadBarangays(previous[1], previous[2]);
        const latitude = document.getElementById('location_latitude');
        const longitude = document.getElementById('location_longitude');
        const coordinates = document.getElementById('selected-coordinates');
        const confirmed = document.getElementById('location_confirmed');
        const confirmButton = document.getElementById('confirm-map-location');
        const searchInput = document.getElementById('map-search');
        const searchResults = document.getElementById('map-search-results');
        const hasInitialPin = Boolean(latitude.value && longitude.value);
        const initial = hasInitialPin ? [Number(latitude.value), Number(longitude.value)] : [11.1547, 123.8056];
        const bantayanBounds = L.latLngBounds([11.0, 123.6], [11.4, 124.0]);
        const map = L.map('booking-location-map', { maxBounds: bantayanBounds, maxBoundsViscosity: 1, minZoom: 11 });
        if (hasInitialPin) map.setView(initial, 17); else map.fitBounds(bantayanBounds);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19, bounds: bantayanBounds, noWrap: true, attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        L.DomEvent.disableClickPropagation(document.getElementById('map-search-panel'));
        L.DomEvent.disableScrollPropagation(document.getElementById('map-search-panel'));
        let marker = null;

        const setPin = (lat, lng, center = false) => {
            if (!bantayanBounds.contains([lat, lng])) {
                alert('Please select a pickup location within Bantayan Island only.');
                return;
            }
            if (!marker) {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', () => { const point = marker.getLatLng(); setPin(point.lat, point.lng); });
            } else marker.setLatLng([lat, lng]);
            latitude.value = Number(lat).toFixed(7);
            longitude.value = Number(lng).toFixed(7);
            confirmed.value = '';
            confirmButton.disabled = false;
            confirmButton.textContent = 'Confirm Location';
            coordinates.textContent = `Pin: ${latitude.value}, ${longitude.value} — drag the marker if needed.`;
            if (center) {
                map.setView([lat, lng], 17);
            }
        };

        const clearPin = () => {
            if (marker) map.removeLayer(marker);
            marker = null;
            latitude.value = '';
            longitude.value = '';
            confirmed.value = '';
            confirmButton.disabled = true;
            confirmButton.textContent = 'Confirm Location';
            coordinates.textContent = 'Choose the address area, then click the exact pickup location.';
        };

        const searchMap = async () => {
            const query = searchInput.value.trim();
            if (!query) return;
            searchResults.classList.remove('hidden');
            searchResults.innerHTML = '<p class="p-3 text-sm text-slate-500">Searching Bantayan Island...</p>';
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=5&countrycodes=ph&q=${encodeURIComponent(query + ', Bantayan Island, Cebu, Philippines')}`);
                const matches = (await response.json()).filter(result => bantayanBounds.contains([Number(result.lat), Number(result.lon)]));
                if (!matches.length) {
                    searchResults.innerHTML = '<p class="p-3 text-sm text-slate-500">No matching location found within Bantayan Island.</p>';
                    return;
                }
                searchResults.innerHTML = '';
                matches.forEach(result => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'block w-full border-b border-slate-100 px-4 py-3 text-left text-sm hover:bg-orange-50 last:border-0';
                    button.textContent = result.display_name;
                    button.addEventListener('click', () => {
                        setPin(Number(result.lat), Number(result.lon), true);
                        searchInput.value = result.display_name;
                        searchResults.classList.add('hidden');
                    });
                    searchResults.appendChild(button);
                });
            } catch (error) {
                searchResults.innerHTML = '<p class="p-3 text-sm text-red-600">Search is temporarily unavailable. You can still click the map.</p>';
            }
        };

        let focusRequest = 0;
        const focusAddress = async (zoom) => {
            const requestId = ++focusRequest;
            const parts = [purok.value, barangay.value, municipality.value, 'Bantayan Island', 'Cebu', 'Philippines'].filter(Boolean);
            if (!municipality.value) {
                map.fitBounds(bantayanBounds);
                return;
            }

            coordinates.textContent = `Finding ${parts.slice(0, -3).join(', ')} on the map...`;
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=ph&q=${encodeURIComponent(parts.join(', '))}`);
                const results = await response.json();
                if (requestId !== focusRequest) return;
                const point = results[0] ? [Number(results[0].lat), Number(results[0].lon)] : null;
                if (point && bantayanBounds.contains(point)) map.flyTo(point, zoom); else map.setZoom(zoom);
            } catch (error) { if (requestId === focusRequest) map.setZoom(zoom); }
            coordinates.textContent = 'Click the map or drag the marker to set the exact pickup location.';
        };

        municipality.addEventListener('change', () => {
            loadBarangays();
            clearPin();
            focusAddress(14);
        });
        barangay.addEventListener('change', () => {
            loadPuroks();
            clearPin();
            focusAddress(16);
        });
        purok.addEventListener('change', () => {
            clearPin();
            focusAddress(18);
        });

        if (hasInitialPin) {
            setPin(initial[0], initial[1]);
            if (confirmed.value) {
                confirmButton.textContent = 'Location Confirmed';
                coordinates.textContent = `Confirmed pin: ${latitude.value}, ${longitude.value}`;
            }
        }
        else if (municipality.value) focusAddress(purok.value ? 18 : (barangay.value ? 16 : 14));
        map.on('click', event => setPin(event.latlng.lat, event.latlng.lng));
        document.getElementById('map-search-button').addEventListener('click', searchMap);
        searchInput.addEventListener('keydown', event => {
            if (event.key === 'Enter') { event.preventDefault(); searchMap(); }
        });
        confirmButton.addEventListener('click', () => {
            if (!latitude.value || !longitude.value) return;
            confirmed.value = '1';
            confirmButton.textContent = 'Location Confirmed';
            coordinates.textContent = `Confirmed pin: ${latitude.value}, ${longitude.value}`;
        });
        document.getElementById('use-current-location').addEventListener('click', () => {
            if (!navigator.geolocation) return alert('Location access is not supported by this browser.');
            navigator.geolocation.getCurrentPosition(
                position => setPin(position.coords.latitude, position.coords.longitude, true),
                () => alert('Your location could not be detected. Please click the map to place the pin.'),
                { enableHighAccuracy: true }
            );
        });
        requestAnimationFrame(() => map.invalidateSize());
    })();
</script>
@endsection
