<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;

$ref = $argv[1] ?? 'BK-20260707-0001';
$booking = Booking::where('booking_reference', $ref)->with('payments')->first();
if (!$booking) {
    echo "Booking not found: {$ref}\n";
    exit(1);
}

echo "Booking: {$booking->booking_reference} total_amount={$booking->total_amount} status={$booking->status} payment_status={$booking->payment_status}\n";
foreach ($booking->payments as $payment) {
    echo "Payment: id={$payment->id}, ref={$payment->payment_reference}, amount={$payment->amount}, method={$payment->payment_method}, status={$payment->status}, created_at={$payment->created_at}\n";
}
