<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$eventId = $argv[1] ?? 1;
$image = $argv[2] ?? 'evenements/ANgH53i1CRschQTzpPc6As9zWoF23fJ9ndath82X.png';
$e = App\Models\Evenement::find($eventId);
if (! $e) {
    echo "event_not_found\n";
    exit(1);
}
$e->image_couverture = $image;
$e->save();
echo "updated: id={$e->id} image_couverture={$e->image_couverture}\n";
