@extends('staff.layout')

@section('content')
@php($pageTitle = 'Bookings')
@if(session('success'))
    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
@endif
<div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="border-b border-slate-200 px-5 py-4 flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-slate-800">Bookings</h2>
            <p class="mt-1 text-sm text-slate-500">Total: {{ $bookings->total() }} bookings</p>
        </div>
    </div>
    <form method="GET" class="flex flex-wrap items-end gap-4 border-b border-slate-200 bg-slate-50 px-5 py-4">
        <div class="min-w-[220px] flex-1">
            <label class="mb-1 block text-sm font-medium text-slate-600">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Booking ref or customer name">
        </div>
        <div class="min-w-[200px]">
            <label class="mb-1 block text-sm font-medium text-slate-600">Status</label>
            <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                <option value="">All statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <button class="rounded-lg bg-brand-600 px-4 py-2 text-white hover:bg-brand-700">Filter</button>
    </form>
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-left">
            <tr>
                <th class="px-5 py-2 font-medium">Reference</th>
                <th class="px-5 py-2 font-medium">Customer</th>
                <th class="px-5 py-2 font-medium">Service</th>
                <th class="px-5 py-2 font-medium">Status</th>
                <th class="px-5 py-2 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($bookings as $booking)
                <tr>
                    <td class="px-5 py-3 font-mono text-xs">{{ $booking->booking_reference }}</td>
                    <td class="px-5 py-3">{{ $booking->customer->full_name ?? '—' }}</td>
                    <td class="px-5 py-3">{{ $booking->service->service_name ?? '—' }}</td>
                    <td class="px-5 py-3"><span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ $booking->status }}</span></td>
                    <td class="px-5 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('staff.bookings.show', $booking) }}" class="text-brand-600 hover:text-brand-700" title="View booking" aria-label="View booking">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('staff.bookings.status', $booking) }}" class="text-brand-600 hover:text-brand-700" title="Update booking" aria-label="Update booking">
                                <i class="fas fa-pen-to-square"></i>
                            </a>
                            @if($staff->role === 'Driver' && $booking->assigned_driver_id === $staff->id && in_array($booking->latestPayment?->status, ['Completed', 'Paid']))
                                <a href="{{ route('staff.receipt', $booking) }}" class="text-emerald-600 hover:text-emerald-700" title="Print Receipt" aria-label="Print Receipt">
                                    <i class="fas fa-print"></i>
                                </a>
                            @endif
                            <form method="POST" action="{{ route('staff.bookings.destroy', $booking) }}" class="inline" onsubmit="return confirm('Delete booking {{ $booking->booking_reference }}? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-700" title="Delete booking" aria-label="Delete booking">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-6 text-center text-slate-400">No bookings assigned.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="{{ $bookings->hasPages() ? 'mt-6 flex justify-center' : '' }}">
    {{ $bookings->links() }}
</div>
@endsection
