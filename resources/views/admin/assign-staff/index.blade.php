@extends('admin.layouts.app')

@section('title', 'Assign Staff')
@section('pageTitle', 'Assign Staff')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h2 class="font-semibold text-slate-800 mb-4">Available Staff</h2>
            <ul class="space-y-2 text-sm">
                @foreach($staffList as $member)
                    <li class="rounded-lg border border-slate-200 px-3 py-2">{{ $member->full_name }} <span class="text-slate-500">({{ $member->role }})</span></li>
                @endforeach
            </ul>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h2 class="font-semibold text-slate-800 mb-4">Unassigned Bookings</h2>
            <ul class="space-y-3 text-sm">
                @forelse($unassignedBookings as $booking)
                    <li class="rounded-lg border border-slate-200 p-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-slate-800">{{ $booking->booking_reference }}</p>
                                <p class="text-slate-500">{{ $booking->customer->full_name ?? '—' }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.assign-staff.store') }}" class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                @if($processors->isNotEmpty())
                                    <select name="staff_id" class="rounded-lg border border-slate-300 px-2 py-1">
                                        @foreach($processors as $member)
                                            <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="rounded-lg bg-brand-600 px-3 py-1.5 text-white">Assign Processor</button>
                                @else
                                    <span class="text-xs text-rose-600">No active processor available</span>
                                @endif
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="text-slate-400">No unassigned bookings.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Assigned Bookings</h2>
        <ul class="space-y-2 text-sm">
            @forelse($assignedBookings as $booking)
                <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                    <span>{{ $booking->booking_reference }} · {{ $booking->assignedStaff->full_name ?? '—' }}</span>
                    <span class="text-slate-500">{{ $booking->status }}</span>
                </li>
            @empty
                <li class="text-slate-400">No assigned bookings.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
