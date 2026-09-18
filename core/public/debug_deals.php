<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$deals = \App\Models\Deal::all();
echo "<h2>All Deals (" . $deals->count() . ")</h2>";
foreach ($deals as $d) {
    echo "<pre>";
    echo "ID: {$d->id}\n";
    echo "Name: {$d->name}\n";
    echo "Status: {$d->status}\n";
    echo "Start: {$d->start_date}\n";
    echo "End: {$d->end_date}\n";
    echo "Items count: " . $d->dealItems()->count() . "\n";
    echo "isExpired: " . ($d->isExpired() ? 'YES' : 'NO') . "\n";
    echo "</pre><hr>";
}

$active = \App\Models\Deal::where('status', 1)->with(['dealItems.item'])->get();
echo "<h2>Status=1 Deals (" . $active->count() . ")</h2>";
foreach ($active as $d) {
    echo "<pre>ID: {$d->id} | Name: {$d->name} | DealItems: " . $d->dealItems->count() . "</pre>";
}
