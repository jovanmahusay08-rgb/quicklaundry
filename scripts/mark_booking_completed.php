<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;

$ref = $argv[1] ?? 'BK-20260707-0001';
$booking = Booking::where('booking_reference', $ref)->first();
if (!$booking) {
    echo "Booking not found: {$ref}\n";
    exit(1);
}

$booking->completed_at = now();
$booking->status = 'Completed';
$booking->save();

echo "Updated booking {$booking->id} ({$booking->booking_reference}) completed_at set to " . $booking->completed_at->toDateTimeString() . "\n";
