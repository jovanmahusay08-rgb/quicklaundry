@extends('customer.layout')

@section('content')
@php($pageTitle = 'Bookings')
<div class="space-y-6">
    <header class="bg-white border-b border-slate-200">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-0 py-2 sm:flex-row sm:items-center sm:justify-between sm:px-6 sm:py-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">My Bookings</h1>
            </div>
            <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center sm:gap-3">
                <x-back-button :href="route('customer.dashboard')">Back to dashboard</x-back-button>
                <a href="{{ route('customer.bookings.create') }}" class="inline-flex items-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Create Booking</a>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl py-2 sm:px-6 sm:py-8">
        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800">Booking History</h2>
            <p class="mt-1 text-sm text-slate-500">Review your past and current laundry requests in one place.</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800">Recent Requests</h3>
                <span class="text-sm text-slate-500">{{ $bookings->count() }} total</span>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-left">
                    <tr>
                        <th class="px-5 py-2 font-medium">Reference</th>
                        <th class="hidden px-5 py-2 font-medium sm:table-cell">Service</th>
                        <th class="px-5 py-2 font-medium">Status</th>
                        <th class="hidden px-5 py-2 font-medium text-right md:table-cell">Total</th>
                        <th class="px-5 py-2 font-medium text-right">Payment</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($bookings as $booking)
                        <tr>
                            <td class="px-3 py-3 font-mono text-[11px] sm:px-5 sm:text-xs"><a class="text-brand-600 hover:underline" href="{{ route('customer.bookings.show', $booking) }}">{{ $booking->booking_reference }}</a></td>
                            <td class="hidden px-5 py-3 sm:table-cell">{{ $booking->service->service_name ?? '—' }}</td>
                            <td class="px-3 py-3 sm:px-5"><span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-700">{{ $booking->status }}</span></td>
                            <td class="hidden px-5 py-3 text-right md:table-cell">₱{{ number_format($booking->total_amount, 2) }}</td>
                            <td class="px-5 py-3 text-right">
                                @if($booking->payment_status !== 'Paid')
                                    <a href="{{ route('customer.bookings.payment.resume', $booking) }}" class="inline-flex rounded-lg bg-brand-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-700">Complete payment</a>
                                @else
                                    <span class="text-xs font-medium text-emerald-600">Paid</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-6 text-center text-slate-400">No bookings yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection
