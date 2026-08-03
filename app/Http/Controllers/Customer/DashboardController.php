<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AppNotification;
use App\Models\Booking;
use App\Models\LaundryService;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index()
    {
        $customer = $this->customer();

        $bookings = $customer->bookings()
            ->with(['service', 'latestPayment'])
            ->latest()
            ->limit(10)
            ->get();

        $activeBookings = $customer->bookings()
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->count();

        $announcements = Announcement::where('is_active', true)
            ->whereIn('visible_to', ['All', 'Customers'])
            ->latest()
            ->limit(5)
            ->get();

        $loyalty = $customer->loyalty;
        $totalSpent = $customer->total_spent;

        if (!$totalSpent) {
            $totalSpent = $customer->bookings()
                ->where('payment_status', 'Paid')
                ->sum('total_amount');
        }

        return view('customer.dashboard', compact('customer', 'bookings', 'activeBookings', 'announcements', 'loyalty', 'totalSpent'));
    }

    public function bookings()
    {
        $customer = $this->customer();
        $bookings = $customer->bookings()->with(['service', 'latestPayment'])->latest()->get();

        return view('customer.bookings.index', compact('customer', 'bookings'));
    }

    public function createBooking()
    {
        $customer = $this->customer();
        $services = LaundryService::where('is_active', true)->get();

        $locations = config('service_locations', []);

        return view('customer.bookings.create', compact('customer', 'services', 'locations'));
    }

    public function storeBooking(Request $request)
    {
        $customer = $this->customer();

        $locations = config('service_locations', []);
        $municipality = $request->string('delivery_municipality')->toString();
        $barangay = $request->string('delivery_barangay')->toString();

        $data = $request->validate([
            'service_id' => ['required', 'exists:laundry_services,id'],
            'quantity_kg' => ['required', 'numeric', 'min:1', 'max:9'],
            'pickup_date' => ['required', 'date'],
            'pickup_time' => ['required', 'date_format:H:i'],
            'delivery_address' => ['required', 'string'],
            'delivery_municipality' => ['required', Rule::in(array_keys($locations))],
            'delivery_barangay' => ['required', Rule::in(array_keys($locations[$municipality] ?? []))],
            'delivery_purok' => ['required', Rule::in($locations[$municipality][$barangay] ?? [])],
            'location_latitude' => ['required', 'numeric', 'between:11.0,11.4'],
            'location_longitude' => ['required', 'numeric', 'between:123.6,124.0'],
            'location_confirmed' => ['accepted'],
            'delivery_phone' => ['required', 'string', 'regex:/^[0-9]{11}$/', 'digits:11'],
            'special_instructions' => ['nullable', 'string'],
        ], [
            'quantity_kg.max' => 'QuickWash only accepts laundry loads up to 9 kg.',
        ]);

        $service = LaundryService::findOrFail($data['service_id']);
        $quantity = (float) $data['quantity_kg'];
        $pricing = $service->calculatePrice($quantity);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'booking_reference' => Booking::generateBookingReference(),
            'service_id' => $service->id,
            'quantity_kg' => $quantity,
            'service_type' => $service->service_name,
            'pickup_date' => $data['pickup_date'],
            'pickup_time' => $data['pickup_time'],
            'service_price' => $pricing['service_price'],
            'subtotal' => $pricing['subtotal'],
            'discount' => 0,
            'total_amount' => $pricing['total_amount'],
            'status' => 'Pending',
            'payment_status' => 'Unpaid',
            'delivery_address' => $data['delivery_address'],
            'delivery_municipality' => $data['delivery_municipality'],
            'delivery_barangay' => $data['delivery_barangay'],
            'delivery_purok' => $data['delivery_purok'],
            'location_latitude' => $data['location_latitude'],
            'location_longitude' => $data['location_longitude'],
            'delivery_phone' => $data['delivery_phone'],
            'special_instructions' => $data['special_instructions'] ?? null,
        ]);

        return redirect()->route('customer.bookings.show', $booking);
    }

    public function showBooking(Booking $booking)
    {
        $customer = $this->customer();

        abort_unless($booking->customer_id === $customer->id, 403);

        $booking->load(['service', 'tracking', 'payments', 'latestPayment']);

        return view('customer.bookings.show', compact('customer', 'booking'));
    }

    public function tracking()
    {
        $customer = $this->customer();
        $bookings = $customer->bookings()->with(['service', 'tracking'])->latest()->get();

        return view('customer.tracking', compact('customer', 'bookings'));
    }

    public function loyalty()
    {
        $customer = $this->customer();
        $loyalty = $customer->loyalty()->first();

        return view('customer.loyalty', compact('customer', 'loyalty'));
    }

    public function notifications()
    {
        $customer = $this->customer();
        $notifications = AppNotification::where('user_type', 'Customer')
            ->where('user_id', $customer->id)
            ->latest('created_at')
            ->get();

        return view('customer.notifications', compact('customer', 'notifications'));
    }

    public function gotoNotification($id)
    {
        $customer = $this->customer();

        $notification = AppNotification::where('user_type', 'Customer')
            ->where('user_id', $customer->id)
            ->findOrFail($id);

        // mark as read
        $notification->is_read = true;
        $notification->save();

        // If related to a booking, redirect there
        if ($notification->related_booking_id) {
            return redirect()->route('customer.bookings.show', $notification->related_booking_id);
        }

        return redirect()->route('customer.notifications');
    }

    public function markAllNotificationsRead()
    {
        $customer = $this->customer();

        AppNotification::where('user_type', 'Customer')
            ->where('user_id', $customer->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function profile()
    {
        $customer = $this->customer();

        return view('customer.profile', compact('customer'));
    }

    public function updateProfile(Request $request)
    {
        $customer = $this->customer();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'unique:customers,email,' . $customer->id],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'barangay' => ['required', 'string'],
        ]);

        $customer->fill($data)->save();

        return back()->with('success', 'Your profile has been updated.');
    }

    public function processPayment(Request $request, Booking $booking)
    {
        $customer = $this->customer();
        
        abort_unless($booking->customer_id === $customer->id, 403);

        if ($booking->payment_status === 'Paid' || $booking->payments()->whereIn('status', ['Paid', 'Completed'])->exists()) {
            return redirect()->route('customer.bookings.show', $booking)
                ->with('info', 'This booking has already been paid.');
        }

        $totalAmount = (float) $booking->total_amount;

        $data = $request->validate([
            'payment_method' => ['required', 'string', 'in:Cash on Delivery,GCash'],
        ]);

        $payment = $booking->payments()->where('status', 'Pending')->latest()->first();
        if (!$payment) {
            $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_reference' => 'PAY-' . now()->format('Ymd') . '-' . str_pad((string) (Payment::count() + 1), 6, '0', STR_PAD_LEFT),
            'amount' => $totalAmount,
            'payment_method' => $data['payment_method'],
            'status' => 'Pending',
            'notes' => 'Payment initiated by customer via ' . $data['payment_method'],
            ]);
        } else {
            $payment->update(['payment_method' => $data['payment_method']]);
        }

        // Update booking payment status to pending
        $booking->payment_status = 'Pending';
        $booking->payment_method = $data['payment_method'];
        $booking->save();

        // Create notification for customer
        AppNotification::create([
            'user_type' => 'Customer',
            'user_id' => $customer->id,
            'title' => 'Payment Initiated',
            'message' => 'Your payment for booking ' . $booking->booking_reference . ' has been initiated. Awaiting confirmation.',
            'type' => 'Payment',
            'related_booking_id' => $booking->id,
            'is_read' => false,
        ]);

        // Create notification for admin
        AppNotification::create([
            'user_type' => 'Admin',
            'user_id' => null,
            'title' => 'Payment Pending Confirmation',
            'message' => 'Payment for booking ' . $booking->booking_reference . ' (' . $data['payment_method'] . ') is pending verification. Amount: ₱' . number_format($totalAmount, 2),
            'type' => 'Payment',
            'related_booking_id' => $booking->id,
            'is_read' => false,
        ]);

        // Handle different payment methods
        if ($data['payment_method'] === 'Cash on Delivery') {
            // Show processing message for cash on delivery
            session(['payment_pending_id' => $payment->id]);
            return redirect()->route('customer.payments.cash-processing', $payment->id)
                ->with('info', 'Please wait for admin to confirm your cash payment.');
        } else {
            // GCash - Redirect to GCash app/gateway
            session(['payment_pending_id' => $payment->id]);
            return redirect()->route('customer.payments.gcash', $payment->id)
                ->with('info', 'Redirecting to GCash...');
        }
    }

    public function resumePayment(Booking $booking)
    {
        $customer = $this->customer();

        abort_unless($booking->customer_id === $customer->id, 403);

        if ($booking->payment_status === 'Paid' || $booking->payments()->whereIn('status', ['Paid', 'Completed'])->exists()) {
            return redirect()->route('customer.bookings.show', $booking)
                ->with('info', 'This booking has already been paid.');
        }

        $payment = $booking->payments()->where('status', 'Pending')->latest()->first();

        if (!$payment) {
            return redirect()->route('customer.bookings.show', $booking)
                ->with('info', 'Choose a payment method to complete your transaction.');
        }

        return match ($payment->payment_method) {
            'GCash' => redirect()->route('customer.payments.gcash', $payment),
            'Cash on Delivery' => redirect()->route('customer.payments.cash-processing', $payment),
            default => redirect()->route('customer.bookings.show', $booking),
        };
    }

    public function showCashPaymentProcessing(Payment $payment)
    {
        $customer = $this->customer();
        $booking = $payment->booking;
        
        abort_unless($booking->customer_id === $customer->id, 403);

        return view('customer.payments.cash-processing', compact('payment', 'booking', 'customer'));
    }

    public function showGcashPayment(Payment $payment)
    {
        $customer = $this->customer();
        $booking = $payment->booking;
        
        abort_unless($booking->customer_id === $customer->id, 403);

        // In a real scenario, this would generate a GCash payment link
        // For now, we'll show a page that simulates GCash redirect
        return view('customer.payments.gcash', compact('payment', 'booking', 'customer'));
    }

    public function submitGcashPayment(Request $request, Payment $payment)
    {
        $customer = $this->customer();
        $booking = $payment->booking;

        abort_unless($booking && $booking->customer_id === $customer->id, 403);
        abort_unless($payment->payment_method === 'GCash' && $payment->status === 'Pending', 422);

        $data = $request->validate([
            'gcash_sender_number' => ['required', 'regex:/^09[0-9]{9}$/'],
            'gcash_reference' => ['required', 'string', 'min:6', 'max:100'],
            'proof_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $path = $request->file('proof_image')->store('payment-proofs', 'public');
        $payment->update([
            'gcash_sender_number' => $data['gcash_sender_number'],
            'gcash_reference' => $data['gcash_reference'],
            'proof_image' => $path,
            'submitted_at' => now(),
            'notes' => 'GCash proof submitted by customer; awaiting driver verification.',
        ]);

        if ($booking->assigned_driver_id) {
            AppNotification::create([
                'user_type' => 'Staff',
                'user_id' => $booking->assigned_driver_id,
                'title' => 'GCash payment awaiting verification',
                'message' => 'GCash proof was submitted for booking '.$booking->booking_reference.'.',
                'type' => 'Payment',
                'related_booking_id' => $booking->id,
                'is_read' => false,
            ]);
        }

        return redirect()->route('customer.bookings.show', $booking)
            ->with('success', 'GCash payment submitted. Please wait for driver verification.');
    }

    public function downloadReceipt(Booking $booking)
    {
        $customer = $this->customer();
        
        abort_unless($booking->customer_id === $customer->id, 403);

        $payment = $booking->payments()->latest()->first();
        abort_unless($payment, 404);

        if ($booking->payment_status !== 'Paid' && $payment->status === 'Paid') {
            $booking->payment_status = 'Paid';
            $booking->save();
        }

        $change = $payment->change_amount;

        return view('customer.receipts.show', compact('booking', 'payment', 'change', 'customer'));
    }

    protected function customer()
    {
        return Auth::guard('customer')->user();
    }
}
