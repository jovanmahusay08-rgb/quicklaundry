<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\LaundryService;
use App\Models\Payment;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_customers' => Customer::count(),
            'total_staff' => Staff::where('is_active', true)->count(),
            'total_services' => LaundryService::where('is_active', true)->count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'Pending')->count(),
            'active_bookings' => Booking::whereNotIn('status', ['Completed', 'Cancelled'])->count(),
            'completed_bookings' => Booking::where('status', 'Completed')->count(),
            'revenue_total' => Booking::where('status', 'Completed')->sum('total_amount'),
            'revenue_today' => Booking::where('status', 'Completed')
                ->where(function($q) {
                    $q->whereDate('completed_at', today())
                      ->orWhereDate('created_at', today());
                })->sum('total_amount'),
            'payments_collected_total' => Payment::whereIn('status', ['Paid', 'Completed'])->sum('amount'),
            'payments_collected_today' => Payment::whereIn('status', ['Paid', 'Completed'])->whereDate('created_at', today())->sum('amount'),
        ];

        $recentBookings = Booking::with(['customer', 'service'])
            ->latest()
            ->limit(8)
            ->get();

        $bookingsByStatus = Booking::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $monthKeys = collect(range(5, 0))
            ->map(fn ($offset) => now()->subMonths($offset)->format('Y-m'));
        $monthlyLabels = $monthKeys
            ->map(fn ($month) => \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y'));
        $monthlyRevenueData = Booking::where('status', 'Completed')
            ->whereBetween('completed_at', [now()->subMonths(5)->startOfMonth(), now()->endOfMonth()])
            ->selectRaw("DATE_FORMAT(completed_at, '%Y-%m') as month, SUM(total_amount) as total")
            ->groupBy('month')
            ->pluck('total', 'month');
        $monthlyRevenue = $monthKeys
            ->map(fn ($month) => (float) ($monthlyRevenueData[$month] ?? 0));

        $paymentsByMethod = Payment::whereIn('status', ['Paid', 'Completed'])
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');

        return view('admin.dashboard', compact(
            'stats', 'recentBookings', 'bookingsByStatus', 'monthlyLabels', 'monthlyRevenue', 'paymentsByMethod'
        ));
    }
}
