<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PaymentProofController extends Controller
{
    public function __invoke(Payment $payment): Response
    {
        $booking = $payment->booking;
        abort_unless($booking, 404);

        $authorized = Auth::guard('admin')->check()
            || (Auth::guard('customer')->check() && $booking->customer_id === Auth::guard('customer')->id())
            || (Auth::guard('staff')->check() && in_array(Auth::guard('staff')->id(), [
                $booking->assigned_staff_id,
                $booking->assigned_driver_id,
            ], true));
        abort_unless($authorized, 403);
        abort_unless($payment->proof_image, 404);

        $disk = Storage::disk('local')->exists($payment->proof_image) ? 'local' : 'public';
        abort_unless(Storage::disk($disk)->exists($payment->proof_image), 404);

        return Storage::disk($disk)->response($payment->proof_image, null, [
            'Cache-Control' => 'no-store, private, max-age=0',
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
