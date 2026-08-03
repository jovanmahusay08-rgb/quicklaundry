@extends('admin.layouts.app')

@section('title', 'Reports')
@section('pageTitle', 'Reports')

@section('content')
<div class="space-y-6">
    <form method="GET" class="bg-white rounded-xl border border-slate-200 p-4 flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">From</label>
            <input type="date" name="from_date" value="{{ $from }}" class="rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">To</label>
            <input type="date" name="to_date" value="{{ $to }}" class="rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <button class="rounded-lg bg-brand-600 px-4 py-2 text-white">Filter</button>
    </form>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="bg-white rounded-xl border border-slate-200 p-5"><p class="text-sm text-slate-500">Total Revenue</p><p class="mt-2 text-2xl font-semibold text-slate-900">₱{{ number_format($totalRevenue, 2) }}</p></div>
        <div class="bg-white rounded-xl border border-slate-200 p-5"><p class="text-sm text-slate-500">Payments Collected</p><p class="mt-2 text-2xl font-semibold text-slate-900">&#8369;{{ number_format($paymentsCollected, 2) }}</p></div>
        <div class="bg-white rounded-xl border border-slate-200 p-5"><p class="text-sm text-slate-500">Bookings</p><p class="mt-2 text-2xl font-semibold text-slate-900">{{ $bookingRange->count() }}</p></div>
        <div class="bg-white rounded-xl border border-slate-200 p-5"><p class="text-sm text-slate-500">Avg. Order Value</p><p class="mt-2 text-2xl font-semibold text-slate-900">₱{{ number_format($averageOrderValue, 2) }}</p></div>
        <div class="bg-white rounded-xl border border-slate-200 p-5"><p class="text-sm text-slate-500">Rush Orders</p><p class="mt-2 text-2xl font-semibold text-slate-900">{{ $rushCount }}</p></div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="mb-4 font-semibold text-slate-800">Bookings by Status</h2>
            <div class="mx-auto max-w-sm"><canvas id="reportStatusChart"></canvas></div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="mb-4 font-semibold text-slate-800">Collected Payments by Method</h2>
            <div class="mx-auto max-w-sm"><canvas id="reportPaymentMethodChart"></canvas></div>
        </div>
        <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="mb-4 font-semibold text-slate-800">Revenue Over the Selected Period</h2>
            <canvas id="dailyRevenueChart" style="max-height:300px;"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Service Performance</h2>
        <ul class="space-y-3 text-sm">
            @foreach($servicePerformance as $name => $stats)
                <li class="rounded-lg bg-slate-50 px-3 py-2 flex items-center justify-between">
                    <span>{{ $name }}</span>
                    <span class="text-slate-500">{{ $stats['bookings'] }} bookings · ₱{{ number_format($stats['revenue'], 2) }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
    const reportStatus = @json($bookingsByStatus);
    const reportPaymentsByMethod = @json($paymentsByMethod);
    const reportDailyRevenue = @json($dailyRevenue);
    const doughnutOptions = { responsive: true, plugins: { legend: { position: 'bottom' } } };

    new Chart(document.getElementById('reportStatusChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(reportStatus),
            datasets: [{ data: Object.values(reportStatus), backgroundColor: ['#f59e0b', '#0284c7', '#8b5cf6', '#10b981', '#ef4444', '#14b8a6'], borderColor: '#fff', borderWidth: 2 }],
        },
        options: doughnutOptions,
    });

    new Chart(document.getElementById('reportPaymentMethodChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(reportPaymentsByMethod),
            datasets: [{ data: Object.values(reportPaymentsByMethod), backgroundColor: ['#0284c7', '#10b981', '#8b5cf6', '#f59e0b'], borderColor: '#fff', borderWidth: 2 }],
        },
        options: doughnutOptions,
    });

    new Chart(document.getElementById('dailyRevenueChart'), {
        type: 'line',
        data: {
            labels: Object.keys(reportDailyRevenue),
            datasets: [{ label: 'Revenue', data: Object.values(reportDailyRevenue), borderColor: '#0284c7', backgroundColor: 'rgba(2, 132, 199, 0.12)', fill: true, tension: 0.35 }],
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } },
    });
</script>
@endsection
