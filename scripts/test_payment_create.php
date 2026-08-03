<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $payment = App\Models\Payment::create([
        'booking_id' => 1,
        'payment_reference' => 'TEST-' . time(),
        'amount' => 100.50,
        'payment_method' => 'Cash',
        'status' => 'Paid',
        'notes' => 'test',
    ]);
    echo "created id=" . $payment->id . PHP_EOL;
} catch (Throwable $e) {
    echo get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
