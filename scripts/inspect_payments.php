<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$payments = App\Models\Payment::latest()->take(10)->get();
echo "count=" . $payments->count() . PHP_EOL;
foreach ($payments as $payment) {
    echo $payment->id . ' | ' . $payment->payment_reference . ' | ' . $payment->status . ' | ' . $payment->payment_method . ' | ' . $payment->amount . PHP_EOL;
}
