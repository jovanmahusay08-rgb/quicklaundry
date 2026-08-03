@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('pageTitle', 'Dashboard')

@section('content')
<div class="grid gap-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total Customers</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stats['total_customers'] }}</p>
        </div>
        <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Active Staff</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stats['total_staff'] }}</p>
        </div>
        <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total Bookings</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stats['total_bookings'] }}</p>
        </div>
        <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Revenue (Bookings) Total</p>
            <p class="mt-3 truncate text-3xl font-bold text-slate-900">₱{{ number_format($stats['revenue_total'] ?? 0, 2) }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-semibold text-slate-800 mb-4">Bookings by Status</h2>
            <p class="text-sm text-slate-500 mb-4">Revenue (Bookings) Total: ₱{{ number_format($stats['revenue_total'], 2) }}</p>
            <div class="mx-auto w-full max-w-sm">
                <canvas id="bookingStatusChart" style="max-height:320px;"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="mb-4 font-semibold text-slate-800">Revenue Trend</h2>
            <p class="mb-4 text-sm text-slate-500">Completed booking revenue for the last six months</p>
            <canvas id="monthlyRevenueChart" style="max-height:320px;"></canvas>
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="mb-4 font-semibold text-slate-800">Payments by Method</h2>
            <div class="mx-auto w-full max-w-md">
                <canvas id="paymentMethodChart" style="max-height:280px;"></canvas>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200">
                <h2 class="font-semibold text-slate-800">Recent Bookings</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-5 py-3 text-left">Reference</th>
                            <th class="px-5 py-3 text-left">Customer</th>
                            <th class="px-5 py-3 text-left">Service</th>
                            <th class="px-5 py-3 text-left">Status</th>
                            <th class="px-5 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentBookings as $b)
                            <tr>
                                <td class="px-5 py-3 font-mono text-xs">{{ $b->booking_reference }}</td>
                                <td class="px-5 py-3">{{ $b->customer->full_name ?? '—' }}</td>
                                <td class="px-5 py-3">{{ $b->service->service_name ?? '—' }}</td>
                                <td class="px-5 py-3"><span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">{{ $b->status }}</span></td>
                                <td class="px-5 py-3 text-right">₱{{ number_format($b->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-6 text-center text-slate-400">No bookings yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
    const ctx = document.getElementById('bookingStatusChart').getContext('2d');
    const bookingsByStatus = @json($bookingsByStatus);

    const labels = Object.keys(bookingsByStatus);
    const data = Object.values(bookingsByStatus);

    const colors = [
        '#10b981', // green for Completed
        '#f59e0b', // amber for Pending
        '#3b82f6', // blue for Pickup Scheduled
        '#8b5cf6', // purple for Picked Up
        '#ef4444', // red for Cancelled
        '#06b6d4', // cyan for In Progress
        '#ec4899', // pink for Washing
        '#6366f1', // indigo for Drying
        '#14b8a6', // teal for Folding
        '#f97316', // orange for Ready for Delivery
    ];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors.slice(0, labels.length),
                borderColor: '#fff',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            size: 12,
                        },
                        padding: 15,
                        boxWidth: 12,
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed;
                        }
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('monthlyRevenueChart'), {
        type: 'line',
        data: {
            labels: @json($monthlyLabels),
            datasets: [{
                label: 'Revenue',
                data: @json($monthlyRevenue),
                borderColor: '#0284c7',
                backgroundColor: 'rgba(2, 132, 199, 0.12)',
                fill: true,
                tension: 0.35,
            }],
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } },
        },
    });

    const paymentsByMethod = @json($paymentsByMethod);
    new Chart(document.getElementById('paymentMethodChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(paymentsByMethod),
            datasets: [{
                label: 'Collected Payments',
                data: Object.values(paymentsByMethod),
                backgroundColor: ['#0284c7', '#10b981', '#8b5cf6', '#f59e0b'],
                borderRadius: 6,
            }],
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } },
        },
    });
</script>
@endsection
