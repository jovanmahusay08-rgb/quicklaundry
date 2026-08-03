<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo 'session.driver=' . config('session.driver') . PHP_EOL;
echo 'session.table=' . config('session.table') . PHP_EOL;
