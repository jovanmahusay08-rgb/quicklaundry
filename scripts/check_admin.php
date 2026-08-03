<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$admin = App\Models\Admin::where('email', 'admin@quickwash.com')->first();
if ($admin) {
    echo "FOUND id={$admin->id} email={$admin->email} is_active=" . ((int)$admin->is_active) . PHP_EOL;
    echo "password={$admin->getAttribute('password')}" . PHP_EOL;
    $ok = password_verify('password123', $admin->getAttribute('password')) ? 'MATCH' : 'NO_MATCH';
    echo "password123_check={$ok}" . PHP_EOL;
} else {
    echo "NOTFOUND" . PHP_EOL;
}
