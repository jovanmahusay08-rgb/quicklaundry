@extends('customer.layout')

@section('content')
@php($pageTitle = 'Dashboard')
<div class="space-y-6">
    {{-- Header removed: navigation moved to sidebar --}}

    <main class="mx-auto max-w-7xl space-y-5 py-1 sm:space-y-6 sm:px-6 sm:py-8">
        <section class="rounded-2xl bg-gradient-to-br from-brand-700 via-brand-600 to-sky-500 p-5 text-white shadow-lg shadow-sky-900/10 sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm text-brand-50">Welcome back</p>
                    <h2 class="text-2xl font-semibold">{{ $customer->full_name }}</h2>
                    <p class="mt-2 text-sm text-brand-50/90">Manage your laundry bookings, view updates, and track every order from one place.</p>
                </div>
                <!-- Primary CTA removed to avoid duplicates -->
            </div>
        </section>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <p class="text-sm font-medium text-slate-500">Active Orders</p>
                <p class="mt-3 text-3xl font-bold text-brand-600">{{ $activeBookings }}</p>
            </div>
            <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <p class="text-sm font-medium text-slate-500">Total Spent</p>
                <p class="mt-3 truncate text-3xl font-bold text-slate-800">₱{{ number_format($totalSpent ?? 0, 2) }}</p>
            </div>
            <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <p class="text-sm font-medium text-slate-500">Loyalty Points</p>
                <p class="mt-3 text-3xl font-bold text-emerald-600">{{ $loyalty->available_points ?? 0 }}</p>
            </div>
        </div>

        {{-- Action cards removed: use sidebar for navigation/actions --}}

        <div class="space-y-6">
            <div class="min-w-0 rounded-xl border border-slate-200 bg-white">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-4 py-4 sm:px-5">
                    <h2 class="font-semibold text-slate-800">Recent Bookings</h2>
                    <a href="{{ route('customer.bookings') }}" class="text-sm text-brand-600 hover:underline">View all</a>
                </div>
                <div class="hidden overflow-x-auto sm:block">
                <table class="w-full min-w-[36rem] text-sm">
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
                                        <a href="{{ route('customer.bookings.payment.resume', $booking) }}" class="text-xs font-medium text-brand-600 hover:underline">Complete payment</a>
                                    @else
                                        <span class="text-xs font-medium text-emerald-600">Paid</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-6 text-center text-slate-400">You haven't booked a service yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>

                <div class="divide-y divide-slate-100 sm:hidden">
                    @forelse ($bookings as $booking)
                        <article class="space-y-3 p-4">
                            <div class="flex min-w-0 items-start justify-between gap-3">
                                <a class="min-w-0 break-all font-mono text-xs font-semibold text-brand-600 hover:underline" href="{{ route('customer.bookings.show', $booking) }}">{{ $booking->booking_reference }}</a>
                                <span class="shrink-0 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ $booking->status }}</span>
                            </div>
                            <div class="flex items-end justify-between gap-3 text-xs">
                                <div class="min-w-0">
                                    <p class="text-slate-400">Service</p>
                                    <p class="truncate font-medium text-slate-700">{{ $booking->service->service_name ?? '—' }}</p>
                                </div>
                                @if($booking->payment_status !== 'Paid')
                                    <a href="{{ route('customer.bookings.payment.resume', $booking) }}" class="shrink-0 rounded-lg bg-brand-50 px-3 py-2 font-semibold text-brand-700">Complete payment</a>
                                @else
                                    <span class="shrink-0 font-semibold text-emerald-600">Paid</span>
                                @endif
                            </div>
                        </article>
                    @empty
                        <p class="px-4 py-6 text-center text-sm text-slate-400">You haven't booked a service yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-slate-800">Announcements</h2>
                    <a href="{{ route('customer.notifications') }}" class="text-sm text-brand-600 hover:underline">View</a>
                </div>
                <ul class="space-y-4 text-sm">
                    @forelse ($announcements as $announcement)
                        <li>
                            <p class="font-medium text-slate-800">{{ $announcement->title }}</p>
                            <p class="text-slate-500">{{ \Illuminate\Support\Str::limit($announcement->content, 100) }}</p>
                        </li>
                    @empty
                        <li class="text-slate-400">No announcements right now.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </main>
</div>
@endsection
