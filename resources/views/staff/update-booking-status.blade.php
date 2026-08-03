@extends('staff.layout')

@section('content')
@php($pageTitle = 'Update Status')
<div class="mx-auto max-w-3xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="mb-4 text-xl font-semibold text-slate-800">Update booking status</h2>
    <form method="POST" action="{{ route('staff.bookings.status.store', $booking) }}" class="space-y-4">
        @csrf
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Status</label>
            <select id="booking-status" name="status" class="w-full rounded-lg border-slate-300">
                @foreach($statusOptions as $status)
                    <option value="{{ $status }}" @selected(old('status', $booking->status) === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        @if($isProcessor)
            <div id="driver-field" class="hidden">
                <label class="mb-1 block text-sm font-medium text-slate-700">Delivery Driver</label>
                <select name="driver_id" class="w-full rounded-lg border-slate-300">
                    <option value="">Choose an active driver</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" @selected(old('driver_id', $booking->assigned_driver_id) == $driver->id)>{{ $driver->full_name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-500">The order will immediately appear in this driver's delivery assignments.</p>
            </div>
        @endif
        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Save</button>
    </form>
</div>
@if($isProcessor)
<script>
    (() => {
        const status = document.getElementById('booking-status');
        const driverField = document.getElementById('driver-field');
        const driver = driverField.querySelector('select');
        const updateDriverField = () => {
            const required = status.value === 'Ready for Delivery';
            driverField.classList.toggle('hidden', !required);
            driver.required = required;
        };
        status.addEventListener('change', updateDriverField);
        updateDriverField();
    })();
</script>
@endif
@endsection
