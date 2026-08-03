@extends('staff.layout')

@section('content')
@php($pageTitle = 'Pickups')
<div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="border-b border-slate-200 px-5 py-4">
        <h2 class="font-semibold text-slate-800">Pickups</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-left">
            <tr>
                <th class="px-5 py-2 font-medium">Booking</th>
                <th class="px-5 py-2 font-medium">Customer</th>
                <th class="px-5 py-2 font-medium">Scheduled</th>
                <th class="px-5 py-2 font-medium">Status</th>
                <th class="px-5 py-2 font-medium">Location</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($pickups as $pickup)
                <tr>
                    <td class="px-5 py-3 font-mono text-xs">{{ $pickup->booking->booking_reference ?? '—' }}</td>
                    <td class="px-5 py-3">{{ $pickup->booking->customer->full_name ?? '—' }}</td>
                    <td class="px-5 py-3">{{ $pickup->scheduled_date?->format('M d, Y') }}</td>
                    <td class="px-5 py-3"><span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ $pickup->status }}</span></td>
                    <td class="px-5 py-3">
                        @if($pickup->booking?->location_latitude !== null && $pickup->booking?->location_longitude !== null)
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $pickup->booking->location_latitude }},{{ $pickup->booking->location_longitude }}" target="_blank" rel="noopener" class="text-brand-600 hover:underline">Directions</a>
                        @else
                            <span class="text-slate-400">No pin</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-6 text-center text-slate-400">No pickup schedules.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
