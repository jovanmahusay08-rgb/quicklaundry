<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use App\Models\Payment;

$ref = $argv[1] ?? 'BK-20260707-0001';
$booking = Booking::where('booking_reference', $ref)->with('payments')->first();
if (!$booking) {
    echo "Booking not found: {$ref}\n";
    exit(1);
}

foreach ($booking->payments as $payment) {
    if ($payment->amount != $booking->total_amount) {
        echo "Fixing payment {$payment->id}: amount {$payment->amount} -> {$booking->total_amount}\n";
        $payment->amount = $booking->total_amount;
        $payment->save();
    } else {
        echo "Payment {$payment->id} already correct.\n";
    }
}
