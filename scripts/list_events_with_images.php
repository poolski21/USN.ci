<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$rows = \App\Models\Evenement::whereNotNull('image_couverture')->get();
if ($rows->isEmpty()) {
    echo "no_events_with_image\n";
    exit(0);
}
foreach ($rows as $e) {
    echo $e->id . ' => ' . $e->image_couverture . PHP_EOL;
}
