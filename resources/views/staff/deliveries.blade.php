@extends('staff.layout')

@section('content')
@php($pageTitle = 'Deliveries')
<div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="border-b border-slate-200 px-5 py-4">
        <h2 class="font-semibold text-slate-800">Deliveries</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-left">
            <tr>
                <th class="px-5 py-2 font-medium">Booking</th>
                <th class="px-5 py-2 font-medium">Customer</th>
                <th class="px-5 py-2 font-medium">Scheduled</th>
                <th class="px-5 py-2 font-medium">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($deliveries as $delivery)
                <tr>
                    <td class="px-5 py-3 font-mono text-xs">{{ $delivery->booking->booking_reference ?? '—' }}</td>
                    <td class="px-5 py-3">{{ $delivery->booking->customer->full_name ?? '—' }}</td>
                    <td class="px-5 py-3">{{ $delivery->scheduled_date?->format('M d, Y') }}</td>
                    <td class="px-5 py-3"><span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ $delivery->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-6 text-center text-slate-400">No delivery schedules.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
