<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
/**
 * Affiche des informations sur le premier Evenement pour debug
 */
$e = App\Models\Evenement::first();
if (! $e) {
	echo "no_event\n";
	exit(0);
}
echo "id: " . $e->id . PHP_EOL;
echo "image_couverture: ";
var_export($e->image_couverture);
echo PHP_EOL;
