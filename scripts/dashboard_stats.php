<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use App\Models\Payment;

$revenue_total = Booking::where('status', 'Completed')->sum('total_amount');
$revenue_today = Booking::where('status', 'Completed')
    ->where(function($q) {
        $q->whereDate('completed_at', today())
          ->orWhereDate('created_at', today());
    })->sum('total_amount');

$payments_collected_total = Payment::whereIn('status', ['Paid', 'Completed'])->sum('amount');
$payments_collected_today = Payment::whereIn('status', ['Paid', 'Completed'])->whereDate('created_at', today())->sum('amount');

echo "revenue_total=" . number_format($revenue_total,2) . PHP_EOL;
echo "revenue_today=" . number_format($revenue_today,2) . PHP_EOL;
echo "payments_collected_total=" . number_format($payments_collected_total,2) . PHP_EOL;
echo "payments_collected_today=" . number_format($payments_collected_today,2) . PHP_EOL;
