@extends('staff.layout')

@section('content')
@php($pageTitle = 'Dashboard')
<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Assigned to me (active)</p>
            <p class="mt-3 text-3xl font-bold text-brand-600">{{ $stats['assigned_active'] }}</p>
        </div>
        <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Driver runs (active)</p>
            <p class="mt-3 text-3xl font-bold text-brand-600">{{ $stats['driver_active'] }}</p>
        </div>
        <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Completed (all-time)</p>
            <p class="mt-3 text-3xl font-bold text-emerald-600">{{ $stats['completed_total'] }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="font-semibold text-slate-800">Assigned Processing Orders</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-5 py-2 font-medium">Reference</th>
                    <th class="px-5 py-2 font-medium">Customer</th>
                    <th class="px-5 py-2 font-medium">Service</th>
                    <th class="px-5 py-2 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($assignedBookings as $b)
                    <tr>
                        <td class="px-5 py-3 font-mono text-xs">{{ $b->booking_reference }}</td>
                        <td class="px-5 py-3">{{ $b->customer->full_name ?? '—' }}</td>
                        <td class="px-5 py-3">{{ $b->service->service_name ?? '—' }}</td>
                        <td class="px-5 py-3"><span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ $b->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-6 text-center text-slate-400">Nothing assigned right now.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="font-semibold text-slate-800">Driver Runs (Pickup / Delivery)</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-5 py-2 font-medium">Reference</th>
                    <th class="px-5 py-2 font-medium">Customer</th>
                    <th class="px-5 py-2 font-medium">Barangay</th>
                    <th class="px-5 py-2 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($driverBookings as $b)
                    <tr>
                        <td class="px-5 py-3 font-mono text-xs">{{ $b->booking_reference }}</td>
                        <td class="px-5 py-3">{{ $b->customer->full_name ?? '—' }}</td>
                        <td class="px-5 py-3">{{ $b->delivery_barangay }}</td>
                        <td class="px-5 py-3"><span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ $b->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-6 text-center text-slate-400">No driver runs assigned.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
