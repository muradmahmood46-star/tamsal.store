<?php
require __DIR__.'/core/vendor/autoload.php';
$app = require_once __DIR__.'/core/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$deals = \App\Models\Deal::all();
echo "<h2>Total Deals: " . $deals->count() . "</h2>";
foreach ($deals as $d) {
    echo "<pre style='background:#f5f5f5;padding:10px;margin:5px'>";
    echo "ID: {$d->id} | Name: {$d->name} | Status: {$d->status}\n";
    echo "Start: {$d->start_date} | End: {$d->end_date}\n";
    echo "DealItems: " . $d->dealItems()->count() . " | isExpired: " . ($d->isExpired() ? 'YES' : 'NO') . "\n";
    echo "</pre>";
}
