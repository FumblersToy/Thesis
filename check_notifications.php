<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$notifications = \App\Models\Notification::orderBy('created_at', 'desc')->get();

echo "Total notifications: " . $notifications->count() . PHP_EOL . PHP_EOL;

foreach ($notifications as $n) {
    echo "ID: {$n->id}" . PHP_EOL;
    echo "User ID: {$n->user_id}" . PHP_EOL;
    echo "Type: {$n->type}" . PHP_EOL;
    echo "Message: {$n->message}" . PHP_EOL;
    echo "Read: " . ($n->read ? 'Yes' : 'No') . PHP_EOL;
    echo "Created: {$n->created_at}" . PHP_EOL;
    echo "---" . PHP_EOL;
}
