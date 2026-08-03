<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Booking;
use App\Models\DeliverySchedule;
use App\Models\Payment;
use App\Models\PickupSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $staff = $this->staff();

        $assignedBookings = $staff->assignedBookings()
            ->with(['customer', 'service'])
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->latest()
            ->get();

        $driverBookings = $staff->driverBookings()
            ->with(['customer', 'service'])
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->latest()
            ->get();

        $stats = [
            'assigned_active' => $assignedBookings->count(),
            'driver_active' => $driverBookings->count(),
            'completed_total' => $staff->assignedBookings()->where('status', 'Completed')->count()
                + $staff->driverBookings()->where('status', 'Completed')->count(),
        ];

        return view('staff.dashboard', compact('staff', 'assignedBookings', 'driverBookings', 'stats'));
    }

    public function bookings(Request $request)
    {
        $staff = $this->staff();
        $query = Booking::with(['customer', 'service', 'latestPayment'])
            ->where(function ($query) use ($staff) {
                $query->where('assigned_staff_id', $staff->id)
                    ->orWhere('assigned_driver_id', $staff->id);
            });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($bookingQuery) use ($search) {
                $bookingQuery->where('booking_reference', 'like', $search)
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('first_name', 'like', $search)
                            ->orWhere('last_name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    });
            });
        }

        $bookings = $query->latest()->paginate(15)->appends($request->query());
        $statuses = ['Pending', 'Pickup Scheduled', 'Picked Up', 'Washing', 'Drying', 'Folding', 'Ready for Delivery', 'Out for Delivery', 'Completed', 'Cancelled'];

        return view('staff.bookings', compact('staff', 'bookings', 'statuses'));
    }

    public function showBooking(Booking $booking)
    {
        $staff = $this->staff();

        abort_unless($booking->assigned_staff_id === $staff->id || $booking->assigned_driver_id === $staff->id, 403);

        $booking->load(['customer', 'service', 'tracking', 'payments', 'latestPayment', 'assignedDriver']);

        return view('staff.booking-details', compact('staff', 'booking'));
    }

    public function destroyBooking(Booking $booking)
    {
        $staff = $this->staff();
        abort_unless($booking->assigned_staff_id === $staff->id || $booking->assigned_driver_id === $staff->id, 403);

        $reference = $booking->booking_reference;
        $booking->delete();

        return redirect()->route('staff.bookings')
            ->with('success', "Booking {$reference} has been deleted.");
    }

    public function updateStatus(Booking $booking)
    {
        $staff = $this->staff();
        abort_unless($booking->assigned_staff_id === $staff->id || $booking->assigned_driver_id === $staff->id, 403);

        $drivers = collect();
        $isProcessor = $booking->assigned_staff_id === $staff->id && $staff->role === 'Processor';
        $statusOptions = $isProcessor
            ? ['Pending', 'Picked Up', 'Washing', 'Drying', 'Folding', 'Ready for Delivery', 'Cancelled']
            : ['Ready for Delivery', 'Out for Delivery', 'Cancelled'];

        if ($isProcessor) {
            $drivers = \App\Models\Staff::where('role', 'Driver')
                ->where('is_active', true)
                ->orderBy('first_name')
                ->get();
        }

        return view('staff.update-booking-status', compact('staff', 'booking', 'drivers', 'isProcessor', 'statusOptions'));
    }

    public function storeStatus(Request $request, Booking $booking)
    {
        $staff = $this->staff();
        abort_unless($booking->assigned_staff_id === $staff->id || $booking->assigned_driver_id === $staff->id, 403);

        if ($booking->status === 'Completed') {
            return redirect()->route('staff.bookings.show', $booking)
                ->with('success', 'This booking is already completed after payment confirmation.');
        }

        $isProcessor = $booking->assigned_staff_id === $staff->id && $staff->role === 'Processor';
        $isDriver = $booking->assigned_driver_id === $staff->id && $staff->role === 'Driver';
        abort_unless($isProcessor || $isDriver, 403);

        $allowedStatuses = $isProcessor
            ? ['Pending', 'Picked Up', 'Washing', 'Drying', 'Folding', 'Ready for Delivery', 'Cancelled']
            : ['Ready for Delivery', 'Out for Delivery', 'Cancelled'];

        $validated = $request->validate([
            'status' => ['required', Rule::in($allowedStatuses)],
            'driver_id' => [
                Rule::requiredIf($isProcessor && $request->input('status') === 'Ready for Delivery'),
                'nullable',
                Rule::exists('staff', 'id')->where(fn ($query) => $query
                    ->where('role', 'Driver')
                    ->where('is_active', true)),
            ],
        ]);

        DB::transaction(function () use ($booking, $validated, $isProcessor) {
            $booking->status = $validated['status'];

            if ($isProcessor && $validated['status'] === 'Ready for Delivery') {
                $booking->assigned_driver_id = $validated['driver_id'];

                DeliverySchedule::updateOrCreate(
                    ['booking_id' => $booking->id],
                    [
                        'scheduled_date' => $booking->delivery_date?->toDateString() ?? today()->toDateString(),
                        'scheduled_time' => $booking->delivery_time ?? now()->format('H:i:s'),
                        'delivery_location' => collect([
                            $booking->delivery_address,
                            $booking->delivery_purok,
                            $booking->delivery_barangay,
                            $booking->delivery_municipality,
                        ])->filter()->join(', '),
                        'delivery_barangay' => $booking->delivery_barangay,
                        'assigned_driver_id' => $validated['driver_id'],
                        'status' => 'Scheduled',
                    ]
                );

                AppNotification::create([
                    'user_type' => 'Staff',
                    'user_id' => $validated['driver_id'],
                    'title' => 'New delivery assigned',
                    'message' => 'Booking '.$booking->booking_reference.' is ready and has been assigned to you for delivery.',
                    'type' => 'Booking',
                    'related_booking_id' => $booking->id,
                    'is_read' => false,
                    'created_at' => now(),
                ]);
            }

            $booking->save();
        });

        $message = $isProcessor && $validated['status'] === 'Ready for Delivery'
            ? 'Laundry processing completed and the delivery driver was notified.'
            : 'Booking status updated.';

        return redirect()->route('staff.bookings.show', $booking)->with('success', $message);
    }

    public function pickups()
    {
        $staff = $this->staff();
        $pickups = PickupSchedule::with(['booking.customer', 'booking.service', 'driver'])
            ->where('assigned_driver_id', $staff->id)
            ->latest('scheduled_date')
            ->get();

        return view('staff.pickups', compact('staff', 'pickups'));
    }

    public function deliveries()
    {
        $staff = $this->staff();
        $deliveries = DeliverySchedule::with(['booking.customer', 'booking.service', 'driver'])
            ->where('assigned_driver_id', $staff->id)
            ->latest('scheduled_date')
            ->get();

        return view('staff.deliveries', compact('staff', 'deliveries'));
    }

    public function receipt(Booking $booking)
    {
        $staff = $this->staff();
        abort_unless($staff->role === 'Driver' && $booking->assigned_driver_id === $staff->id, 403);

        $booking->load('latestPayment');

        if ((!$booking->latestPayment || $booking->latestPayment->status === 'Failed') && $booking->payment_status !== 'Paid') {
            Payment::create([
                'booking_id' => $booking->id,
                'payment_reference' => 'PAY-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                'amount' => $booking->total_amount,
                'payment_method' => $booking->payment_method ?: 'Cash on Delivery',
                'status' => 'Pending',
                'notes' => 'Payment prepared for confirmation by the assigned delivery driver.',
            ]);

            $booking->load('latestPayment');
        }

        if ($booking->latestPayment?->payment_method === 'GCash' && !$booking->latestPayment->submitted_at) {
            return redirect()->route('staff.bookings.show', $booking)
                ->with('error', 'The customer has not submitted GCash payment proof yet.');
        }

        return view('staff.generate-receipt', compact('staff', 'booking'));
    }

    public function confirmPayment(Request $request, Payment $payment)
    {
        $staff = $this->staff();
        $booking = $payment->booking;
        abort_unless($booking && $staff->role === 'Driver' && $booking->assigned_driver_id === $staff->id, 403);

        $action = $request->input('action');
        $notes = $request->input('notes');
        $gcashTransactionId = $request->input('gcash_transaction_id') ?: $payment->gcash_reference;
        $amountReceived = $request->input('amount_received');

        if ($action === 'confirm') {
            $request->validate([
                'amount_received' => $payment->payment_method === 'Cash on Delivery'
                    ? ['required', 'numeric', 'min:' . $payment->amount]
                    : ['nullable', 'numeric'],
                'gcash_transaction_id' => $payment->payment_method === 'GCash'
                    ? ['required', 'string', 'max:255']
                    : ['nullable', 'string', 'max:255'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ]);

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

            // A confirmed payment completes the assigned booking automatically.
            $booking->payment_status = 'Paid';
            $booking->status = 'Completed';
            $booking->completed_at = now();
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

            return redirect()->route('staff.receipt', $booking->id)
                ->with('success', 'Payment confirmed! The booking is now completed and receipts are available for staff and customer.');
        } else {
            $payment->status = 'Failed';
            $payment->notes = 'Rejected by staff. ' . ($notes ?? '');
            $payment->save();

            $booking->payment_status = 'Unpaid';
            $booking->save();

            AppNotification::create([
                'user_type' => 'Customer',
                'user_id' => $booking->customer_id,
                'title' => 'Payment Rejected',
                'message' => 'Your payment for booking ' . $booking->booking_reference . ' could not be verified. Please contact support.',
                'type' => 'Payment',
                'related_booking_id' => $booking->id,
                'is_read' => false,
            ]);

            return redirect()->route('staff.receipt', $booking->id)
                ->with('error', 'Payment has been rejected.');
        }
    }

    public function profile()
    {
        $staff = $this->staff();

        return view('staff.profile', compact('staff'));
    }

    public function gotoNotification($id)
    {
        $staff = $this->staff();

        $notification = AppNotification::where('user_type', 'Staff')
            ->where('user_id', $staff->id)
            ->findOrFail($id);

        $notification->is_read = true;
        $notification->save();

        if ($notification->related_booking_id) {
            return redirect()->route('staff.bookings.show', $notification->related_booking_id);
        }

        return redirect()->route('staff.dashboard');
    }

    public function notifications()
    {
        $staff = $this->staff();
        $notifications = AppNotification::where('user_type', 'Staff')
            ->where('user_id', $staff->id)
            ->latest('created_at')
            ->get();

        return view('staff.notifications', compact('staff', 'notifications'));
    }

    public function markAllNotificationsRead()
    {
        $staff = $this->staff();

        AppNotification::where('user_type', 'Staff')
            ->where('user_id', $staff->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    protected function staff()
    {
        return Auth::guard('staff')->user();
    }
}
