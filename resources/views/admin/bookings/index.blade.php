@extends('admin.layouts.app')

@section('title', 'Bookings')
@section('pageTitle', 'Bookings')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-3"> 
                <svg class="h-5 w-5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"></path></svg>
                Bookings List
            </h3>
            <p class="text-sm text-slate-500">Total: {{ $bookings->total() }} bookings</p>
        </div>
    </div>
    <form method="GET" class="bg-white rounded-xl border border-slate-200 p-4 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[220px]">
            <label class="block text-sm font-medium text-slate-600 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Booking ref or customer name">
        </div>
        <div class="min-w-[220px]">
            <label class="block text-sm font-medium text-slate-600 mb-1">Status</label>
            <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                <option value="">All statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="ml-auto flex items-center gap-2">
            <button class="rounded-lg bg-brand-600 px-4 py-2 text-white">Filter</button>
            <button type="button" id="select-all-bookings" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-slate-700 hover:bg-slate-50">Select All</button>
            <button type="submit" form="booking-bulk-delete-form" id="delete-selected-bookings" class="rounded-lg bg-rose-600 px-4 py-2 text-white hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-50" disabled>Delete Selected</button>
        </div>
    </form>
    <form id="booking-bulk-delete-form" method="POST" action="{{ route('admin.bookings.destroy-selected') }}" onsubmit="return confirm('Delete all selected bookings? This cannot be undone.');">
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
                        <th class="px-5 py-3 text-left">Service</th>
                        <th class="px-5 py-3 text-left">Amount</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-left">Type</th>
                        <th class="px-5 py-3 text-left">Date</th>
                        <th class="px-5 py-3 text-left">Time</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings as $booking)
                        <tr>
                            <td class="px-5 py-3">
                                <input type="checkbox" name="booking_ids[]" value="{{ $booking->id }}" form="booking-bulk-delete-form" class="booking-select rounded border-slate-300 text-brand-600 focus:ring-brand-500" aria-label="Select booking {{ $booking->booking_reference }}">
                            </td>
                            <td class="px-5 py-3 font-mono text-xs text-slate-700">{{ $booking->booking_reference }}</td>
                            <td class="px-5 py-3">
                                <div class="font-medium text-slate-800">{{ $booking->customer->full_name ?? '—' }}</div>
                                <div class="text-xs text-slate-500">{{ $booking->customer->email ?? '' }}</div>
                            </td>
                            <td class="px-5 py-3">{{ $booking->service->service_name ?? '—' }}</td>
                            <td class="px-5 py-3 font-semibold">₱{{ number_format($booking->total_amount, 2) }}</td>
                            <td class="px-5 py-3">
                                @php
                                    $s = strtoupper($booking->status ?? 'PENDING');
                                    $badge = $s === 'COMPLETED' ? 'bg-emerald-100 text-emerald-700' : ($s === 'PENDING' ? 'bg-amber-100 text-amber-700' : ($s === 'CANCELLED' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700'));
                                @endphp
                                <span class="rounded-full px-3 py-1 text-xs {{ $badge }}">{{ $s }}</span>
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-700">{{ $booking->is_rush_service ? 'RUSH' : 'NORMAL' }}</td>
                            <td class="px-5 py-3 text-sm text-slate-500">{{ $booking->created_at?->format('M d, Y') }}</td>
                            <td class="px-5 py-3 text-sm text-slate-500">{{ $booking->created_at?->format('h:i A') }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="inline-flex items-center justify-center w-8 h-8 rounded border border-slate-200 mr-2 hover:bg-slate-50" title="View">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.bookings.show', [$booking->id, 'edit' => 1]) }}" class="inline-flex items-center justify-center w-8 h-8 rounded border border-slate-200 hover:bg-slate-50 text-amber-600" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" class="inline" onsubmit="return confirm('Delete booking {{ $booking->booking_reference }}? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-2 inline-flex h-8 w-8 items-center justify-center rounded border border-rose-200 text-rose-600 hover:bg-rose-50" title="Delete" aria-label="Delete booking">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M10 11v6M14 11v6M9 7l1-2h4l1 2M8 7l1 13h6l1-13"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-5 py-6 text-center text-slate-400">No bookings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="{{ $bookings->hasPages() ? 'flex justify-center' : '' }}">
        {{ $bookings->links() }}
    </div>
</div>

<script>
    const bookingSelectAll = document.getElementById('select-all-bookings');
    const bookingDeleteSelected = document.getElementById('delete-selected-bookings');
    const bookingCheckboxes = [...document.querySelectorAll('.booking-select')];

    const updateBookingSelection = () => {
        const selectedCount = bookingCheckboxes.filter((checkbox) => checkbox.checked).length;
        bookingDeleteSelected.disabled = selectedCount === 0;
        const allSelected = selectedCount === bookingCheckboxes.length && selectedCount > 0;
        bookingSelectAll.textContent = allSelected ? 'Clear Selection' : 'Select All';
    };

    bookingSelectAll.addEventListener('click', () => {
        const shouldSelect = bookingCheckboxes.some((checkbox) => !checkbox.checked);
        bookingCheckboxes.forEach((checkbox) => checkbox.checked = shouldSelect);
        updateBookingSelection();
    });

    bookingCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', updateBookingSelection));
</script>
@endsection
