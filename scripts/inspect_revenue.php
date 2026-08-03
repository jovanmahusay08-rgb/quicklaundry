<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;

$sum = Booking::where('status','Completed')->sum('total_amount');
echo "completed_bookings_sum=" . $sum . PHP_EOL;
$bookings = Booking::where('status','Completed')->get();
foreach($bookings as $b) {
    echo $b->id . ' | ' . ($b->booking_reference ?? '') . ' | ' . ($b->customer?->full_name ?? '') . ' | ' . $b->total_amount . ' | ' . $b->status . ' | ' . ($b->completed_at?->format('Y-m-d H:i:s') ?? 'null') . PHP_EOL;
}
