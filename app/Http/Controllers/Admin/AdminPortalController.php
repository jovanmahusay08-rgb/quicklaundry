<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Announcement;
use App\Models\AppNotification;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\LaundryService;
use App\Models\PasswordResetCode;
use App\Models\Payment;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminPortalController extends Controller
{
    public function bookings(Request $request)
    {
        $query = Booking::query()->with(['customer', 'service']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('booking_reference', 'like', $search)
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('first_name', 'like', $search)
                            ->orWhere('last_name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    });
            });
        }

        $bookings = $query->latest()->paginate(15)->appends($request->query());
        $statuses = ['Pending', 'Pickup Scheduled', 'Picked Up', 'Washing', 'Drying', 'Folding', 'Ready for Delivery', 'Out for Delivery', 'Completed', 'Cancelled'];

        return view('admin.bookings.index', compact('bookings', 'statuses'));
    }

    public function showBooking(Booking $booking)
    {
        $booking->load(['customer', 'service', 'assignedStaff', 'tracking', 'payments']);

        // If payments exist and sum covers total_amount but booking payment_status not updated, fix it.
        $paidAmount = $booking->payments()->whereIn('status', ['Paid', 'Completed'])->sum('amount');
        if ($paidAmount >= $booking->total_amount && $booking->payment_status !== 'Paid') {
            $booking->payment_status = 'Paid';
            $booking->save();
        }

        return view('admin.bookings.show', compact('booking'));
    }

    public function destroyBooking(Booking $booking)
    {
        $reference = $booking->booking_reference;

        $booking->delete();

        return redirect()->route('admin.bookings')
            ->with('success', "Booking {$reference} has been deleted.");
    }

    public function destroyBookings(Request $request)
    {
        $data = $request->validate([
            'booking_ids' => ['required', 'array', 'min:1'],
            'booking_ids.*' => ['integer', 'exists:bookings,id'],
        ]);

        Booking::whereIn('id', $data['booking_ids'])->delete();

        return redirect()->route('admin.bookings')
            ->with('success', 'Selected bookings have been deleted.');
    }

    public function customers(Request $request)
    {
        $customers = Customer::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . $request->search . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', $search)
                        ->orWhere('last_name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('phone', 'like', $search);
                });
            })
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        return view('admin.customers.index', compact('customers'));
    }

    public function showCustomer(Customer $customer)
    {
        $customer->load('bookings', 'loyalty');
        $loyalty = $customer->loyalty()->first();
        $recentBookings = $customer->bookings()->with('service')->latest()->limit(10)->get();
        $payments = Payment::whereHas('booking', function ($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })->latest()->limit(10)->get();

        return view('admin.customers.show', compact('customer', 'loyalty', 'recentBookings', 'payments'));
    }

    public function staff(Request $request)
    {
        $staff = Staff::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . $request->search . '%';
                $query->where(function ($staffQuery) use ($search) {
                    $staffQuery->where('first_name', 'like', $search)
                        ->orWhere('last_name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('phone', 'like', $search);
                });
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->where('role', $request->role);
            })
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        $staffStats = [
            'total' => Staff::count(),
            'active' => Staff::where('is_active', true)->count(),
            'drivers' => Staff::where('role', 'Driver')->count(),
        ];

        return view('admin.staff.index', compact('staff', 'staffStats'));
    }

    public function storeStaff(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:100', 'unique:staff,email'],
            'phone' => ['required', 'digits:11'],
            'role' => ['required', 'in:Driver,Processor,Quality Check'],
            'address' => ['nullable', 'string'],
            'barangay' => ['nullable', 'in:Balidbid,Bantigue,Langub,Maricaban,Okoy,Poblacion,Pooc,Talisay'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'hire_date' => ['nullable', 'date'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Staff::create($validated + ['is_active' => true]);

        return redirect()->route('admin.staff')
            ->with('success', 'Staff account created successfully.');
    }

    public function editStaff(Staff $staff)
    {
        return view('admin.staff.edit', compact('staff'));
    }

    public function updateStaff(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:100', Rule::unique('staff', 'email')->ignore($staff->id)],
            'phone' => ['required', 'digits:11'],
            'role' => ['required', 'in:Driver,Processor,Quality Check'],
            'address' => ['nullable', 'string'],
            'barangay' => ['nullable', 'in:Balidbid,Bantigue,Langub,Maricaban,Okoy,Poblacion,Pooc,Talisay'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'hire_date' => ['nullable', 'date'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $password = $validated['password'] ?? null;
        unset($validated['password']);
        if ($password) {
            $validated['password'] = Hash::make($password);
        }
        $staff->update($validated);

        return redirect()->route('admin.staff')
            ->with('success', 'Staff account updated successfully.');
    }

    public function destroyStaff(Staff $staff)
    {
        DB::transaction(function () use ($staff) {
            Booking::where('assigned_staff_id', $staff->id)->update(['assigned_staff_id' => null]);
            Booking::where('assigned_driver_id', $staff->id)->update(['assigned_driver_id' => null]);
            DB::table('pickup_schedule')->where('assigned_driver_id', $staff->id)->update(['assigned_driver_id' => null]);
            DB::table('delivery_schedule')->where('assigned_driver_id', $staff->id)->update(['assigned_driver_id' => null]);
            PasswordResetCode::where('portal', 'staff')->where('email', strtolower($staff->email))->delete();
            $staff->delete();
        });

        return redirect()->route('admin.staff')
            ->with('success', 'Staff account deleted successfully. Existing orders were left unassigned.');
    }

    public function services()
    {
        $services = LaundryService::latest()->paginate(15);

        return view('admin.services.index', compact('services'));
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'price_per_kilo' => 'required|numeric|min:0',
            'pricing_type' => ['required', 'in:variable,flat'],
        ]);

        LaundryService::create($validated + ['is_active' => true]);

        return back()->with('success', 'Service created successfully.');
    }

    public function updateService(Request $request, LaundryService $service)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'price_per_kilo' => 'required|numeric|min:0',
            'pricing_type' => ['required', 'in:variable,flat'],
        ]);

        $service->update($validated);

        return back()->with('success', 'Service updated successfully.');
    }

    public function destroyService(LaundryService $service)
    {
        $service->delete();

        return back()->with('success', 'Service removed successfully.');
    }

    public function reports(Request $request)
    {
        $from = $request->input('from_date', now()->startOfMonth()->toDateString());
        $to = $request->input('to_date', now()->endOfMonth()->toDateString());
        $start = $from . ' 00:00:00';
        $end = $to . ' 23:59:59';

        $completedBookings = Booking::whereNotNull('completed_at')
            ->whereBetween('completed_at', [$start, $end])
            ->get();

        $bookingRange = Booking::with('service')->whereBetween('created_at', [$start, $end])->get();
        $servicePerformance = $bookingRange->groupBy(function ($booking) {
            return $booking->service->service_name ?? 'Unassigned';
        })->map(function ($items) {
            return [
                'bookings' => $items->count(),
                'revenue' => $items->sum('total_amount'),
            ];
        })->sortByDesc('revenue');

        $totalRevenue = $completedBookings->sum('total_amount');
        $averageOrderValue = $bookingRange->count() ? $totalRevenue / $bookingRange->count() : 0;
        $rushCount = $bookingRange->where('is_rush_service', true)->count();

        $bookingsByStatus = $bookingRange->groupBy('status')->map->count();
        $paymentsCollected = Payment::whereIn('status', ['Paid', 'Completed'])
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');
        $paymentsByMethod = Payment::whereIn('status', ['Paid', 'Completed'])
            ->whereBetween('created_at', [$start, $end])
            ->select('payment_method', \Illuminate\Support\Facades\DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');
        $dailyRevenue = $completedBookings
            ->groupBy(fn ($booking) => ($booking->completed_at ?? $booking->created_at)->format('M d'))
            ->map(fn ($bookings) => $bookings->sum('total_amount'));

        return view('admin.reports.index', compact(
            'from', 'to', 'totalRevenue', 'averageOrderValue', 'rushCount', 'servicePerformance', 'bookingRange',
            'bookingsByStatus', 'paymentsCollected', 'paymentsByMethod', 'dailyRevenue'
        ));
    }

    public function payments(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');
        $payments = Payment::query()
            ->with(['booking' => function ($query) {
                $query->with('customer');
            }])
            ->when($search, function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($paymentQuery) use ($term) {
                    $paymentQuery->where('payment_reference', 'like', $term)
                        ->orWhereHas('booking', function ($bookingQuery) use ($term) {
                            $bookingQuery->where('booking_reference', 'like', $term)
                                ->orWhereHas('customer', function ($customerQuery) use ($term) {
                                    $customerQuery->where('first_name', 'like', $term)
                                        ->orWhere('last_name', 'like', $term)
                                        ->orWhere('email', 'like', $term);
                                });
                        });
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->whereIn('status', match ($status) {
                    'Paid', 'Completed', 'Completed Payment', 'completed' => ['Paid', 'Completed'],
                    'Pending' => ['Pending'],
                    'Failed' => ['Failed'],
                    default => ['Paid', 'Completed'],
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('admin.payments.index', compact('payments'));
    }

    public function showPayment(\App\Models\Payment $payment)
    {
        $payment->load(['booking.customer']);

        return view('admin.payments.show', compact('payment'));
    }

    public function destroyPayment(Payment $payment)
    {
        $reference = $payment->payment_reference;
        $payment->delete();

        return redirect()->route('admin.payments')
            ->with('success', "Payment {$reference} has been deleted.");
    }

    public function destroyPayments(Request $request)
    {
        $data = $request->validate([
            'payment_ids' => ['required', 'array', 'min:1'],
            'payment_ids.*' => ['integer', 'exists:payments,id'],
        ]);

        Payment::whereIn('id', $data['payment_ids'])->delete();

        return redirect()->route('admin.payments')
            ->with('success', 'Selected payments have been deleted.');
    }

    protected function normalizePaymentStatus(string $status): string
    {
        return match ($status) {
            'Completed', 'Completed Payment', 'completed' => 'Paid',
            'Paid' => 'Paid',
            'Pending' => 'Pending',
            'Failed' => 'Failed',
            default => 'Paid',
        };
    }

    public function announcements()
    {
        $announcements = Announcement::with('admin')->latest()->paginate(10);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function storeAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'content' => 'required|string',
            'visible_to' => 'nullable|string|max:50',
        ]);

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'visible_to' => $validated['visible_to'] ?? 'All',
            'is_active' => true,
            'created_by' => auth('admin')->id(),
        ]);

        $customers = Customer::where('is_active', true)->get();
        foreach ($customers as $customer) {
            AppNotification::create([
                'user_type' => 'Customer',
                'user_id' => $customer->id,
                'title' => 'New Announcement',
                'message' => $announcement->title,
                'type' => 'System',
                'related_booking_id' => null,
                'is_read' => false,
                'created_at' => now(),
            ]);
        }

        $staff = Staff::where('is_active', true)->get();
        foreach ($staff as $member) {
            AppNotification::create([
                'user_type' => 'Staff',
                'user_id' => $member->id,
                'title' => 'New Announcement',
                'message' => $announcement->title,
                'type' => 'System',
                'related_booking_id' => null,
                'is_read' => false,
                'created_at' => now(),
            ]);
        }

        return back()->with('success', 'Announcement created successfully.');
    }

    public function destroyAnnouncement(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'Announcement removed successfully.');
    }

    public function gotoNotification($id)
    {
        $notification = AppNotification::where('user_type', 'Admin')
            ->where(function ($query) {
                $query->where('user_id', auth('admin')->id())->orWhereNull('user_id');
            })
            ->findOrFail($id);

        $notification->is_read = true;
        $notification->save();

        if ($notification->related_booking_id) {
            return redirect()->route('admin.bookings.show', $notification->related_booking_id);
        }

        return redirect()->route('admin.dashboard');
    }

    public function notifications()
    {
        $notifications = AppNotification::where('user_type', 'Admin')
            ->where(function ($query) {
                $query->where('user_id', auth('admin')->id())->orWhereNull('user_id');
            })
            ->latest('created_at')
            ->get();

        return view('admin.notifications', compact('notifications'));
    }

    public function markAllNotificationsRead()
    {
        AppNotification::where('user_type', 'Admin')
            ->where(function ($query) {
                $query->where('user_id', auth('admin')->id())->orWhereNull('user_id');
            })
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function assignStaff()
    {
        $unassignedBookings = Booking::query()
            ->whereNull('assigned_staff_id')
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->with(['customer', 'service'])
            ->latest()
            ->get();

        $assignedBookings = Booking::query()
            ->whereNotNull('assigned_staff_id')
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->with(['customer', 'service', 'assignedStaff'])
            ->latest()
            ->get();

        $staffList = Staff::where('is_active', true)->orderBy('first_name')->get();
        $processors = $staffList->where('role', 'Processor');

        return view('admin.assign-staff.index', compact('unassignedBookings', 'assignedBookings', 'staffList', 'processors'));
    }

    public function assignStaffStore(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'staff_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('staff', 'id')->where(fn ($query) => $query
                    ->where('role', 'Processor')
                    ->where('is_active', true)),
            ],
        ]);

        $booking = Booking::findOrFail($validated['booking_id']);
        $booking->assigned_staff_id = $validated['staff_id'];
        $booking->save();

        $staff = Staff::findOrFail($validated['staff_id']);
        AppNotification::create([
            'user_type' => 'Staff',
            'user_id' => $staff->id,
            'title' => 'New task assigned',
            'message' => 'A new booking has been assigned to you: ' . ($booking->booking_reference ?? 'Booking #' . $booking->id),
            'type' => 'Booking',
            'related_booking_id' => $booking->id,
            'is_read' => false,
            'created_at' => now(),
        ]);

        return back()->with('success', 'Staff assigned successfully.');
    }

    public function settings()
    {
        $admin = auth('admin')->user();
        $stats = [
            'customers' => Customer::count(),
            'staff' => Staff::count(),
            'bookings' => Booking::count(),
            'revenue' => Booking::where('status', 'Completed')->sum('total_amount'),
            'this_month' => Booking::where('status', 'Completed')->whereMonth('completed_at', now()->month)->sum('total_amount'),
        ];

        return view('admin.settings.index', compact('admin', 'stats'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $admin = auth('admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $admin->password = Hash::make($request->password);
        $admin->save();

        return back()->with('success', 'Password updated successfully.');
    }

    public function updateProfile(Request $request)
    {
        $admin = auth('admin')->user();
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:100', Rule::unique('admins', 'email')->ignore($admin->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => ['required', 'string'],
        ]);

        if (!Hash::check($validated['current_password'], $admin->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.'])
                ->withInput($request->except(['current_password']));
        }

        unset($validated['current_password']);
        $admin->update($validated);

        return back()->with('success', 'Admin details updated successfully.');
    }

    public function toggleStaffActive(Staff $staff)
    {
        $staff->is_active = !$staff->is_active;
        $staff->save();

        $status = $staff->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Staff member {$staff->full_name} has been {$status}.");
    }

    public function confirmPayment(Request $request, Payment $payment)
    {
        if ($payment->status !== 'Pending') {
            return redirect()->route('admin.payments.show', $payment->id)
                ->with('info', 'This payment has already been confirmed and can only be viewed or printed.');
        }

        $action = $request->input('action');
        $notes = $request->input('notes');
        $gcashTransactionId = $request->input('gcash_transaction_id');
        $amountReceived = $request->input('amount_received');

        if ($action === 'confirm') {
            // Update payment status to Completed
            $payment->status = 'Completed';
            $payment->notes = $notes ?? $payment->notes;
            
            if ($amountReceived && $payment->payment_method === 'Cash on Delivery') {
                $payment->notes .= ' | Amount Received: ₱' . number_format((float)$amountReceived, 2);
                if ((float)$amountReceived > (float)$payment->amount) {
                    $change = (float)$amountReceived - (float)$payment->amount;
                    $payment->notes .= ' | Change: ₱' . number_format($change, 2);
                }
            }
            if ($gcashTransactionId) {
                $payment->notes .= ' | GCash Transaction ID: ' . $gcashTransactionId;
            }
            $payment->save();

            // Update booking payment status to Paid
            $booking = $payment->booking;
            $booking->payment_status = 'Paid';
            $booking->save();

            // Create customer notification
            AppNotification::create([
                'user_type' => 'Customer',
                'user_id' => $booking->customer_id,
                'title' => 'Payment Confirmed',
                'message' => 'Your payment for booking ' . $booking->booking_reference . ' has been confirmed and verified. Receipt has been generated.',
                'type' => 'Payment',
                'related_booking_id' => $booking->id,
                'is_read' => false,
            ]);

            return redirect()->route('admin.payments.show', $payment->id)
                ->with('success', 'Payment confirmed! Receipt has been generated for both admin and customer.');
        } else {
            // Reject payment
            $payment->status = 'Failed';
            $payment->notes = 'Rejected by admin. ' . ($notes ?? '');
            $payment->save();

            // Reset booking payment status
            $booking = $payment->booking;
            $booking->payment_status = 'Unpaid';
            $booking->save();

            // Create customer notification
            AppNotification::create([
                'user_type' => 'Customer',
                'user_id' => $booking->customer_id,
                'title' => 'Payment Rejected',
                'message' => 'Your payment for booking ' . $booking->booking_reference . ' could not be verified. Please contact support.',
                'type' => 'Payment',
                'related_booking_id' => $booking->id,
                'is_read' => false,
            ]);

            return redirect()->route('admin.payments.show', $payment->id)
                ->with('info', 'Payment rejected. Customer has been notified.');
        }
    }
}
