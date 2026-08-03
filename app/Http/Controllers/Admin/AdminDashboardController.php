<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Notification;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_customers' => Customer::where('is_active', true)->count(),
            'total_staff' => Staff::where('is_active', true)->count(),
            'total_bookings' => Booking::count(),
            'revenue_total' => Booking::where('status', 'Completed')->sum('total_amount'),
            'revenue_today' => Booking::where('status', 'Completed')
                ->where(function($q) {
                    $q->whereDate('completed_at', today())
                      ->orWhereDate('created_at', today());
                })->sum('total_amount'),
            'pending_orders' => Booking::whereIn('status', ['Pending','Pickup Scheduled','Picked Up','Washing','Drying','Folding','Ready for Delivery','Out for Delivery'])->count(),
            'rush_orders' => Booking::where('is_rush_service', 1)->whereNotIn('status', ['Completed','Cancelled'])->count(),
            'completed_today' => Booking::whereDate('completed_at', today())->count(),
        ];

        $monthly_revenue = Booking::where('status', 'Completed')->whereMonth('completed_at', now()->month)->sum('total_amount');
        $recentBookings = Booking::with(['customer', 'service'])->latest()->limit(10)->get();

        $bookingsByStatus = Booking::select('status', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Notifications from `notifications` table
        $admin = auth()->guard('admin')->user();
        $notifications = Notification::where('user_type', 'Admin')
                                     ->where('user_id', $admin->id)
                                     ->with('relatedBooking')
                                     ->latest()
                                     ->limit(20)
                                     ->get();
        $unreadCount = Notification::where('user_type', 'Admin')
                                   ->where('user_id', $admin->id)
                                   ->where('is_read', false)
                                   ->count();

        // Pending bookings as "live" notifications
        $newBookingNotifs = Booking::with(['customer', 'service'])
                                   ->where('status', 'Pending')
                                   ->latest()
                                   ->limit(5)
                                   ->get();

        $totalUnread = $unreadCount + $newBookingNotifs->count();

        return view('admin.dashboard', compact('stats', 'monthly_revenue', 'recentBookings', 'bookingsByStatus', 'notifications', 'newBookingNotifs', 'totalUnread'));
    }
}