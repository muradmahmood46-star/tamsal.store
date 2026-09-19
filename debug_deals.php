<?php
require __DIR__.'/core/vendor/autoload.php';
$app = require_once __DIR__.'/core/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check deals table columns
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('deals');
echo "<h2>Deals Table Columns:</h2><pre>" . implode(', ', $columns) . "</pre>";

// Add photo column if missing
if (!in_array('photo', $columns)) {
    \Illuminate\Support\Facades\Schema::table('deals', function ($table) {
        $table->string('photo')->nullable()->after('slug');
    });
    echo "<p style='color:green'>✅ photo column added!</p>";
} else {
    echo "<p style='color:blue'>ℹ️ photo column already exists.</p>";
}

// Show FIRST lines of log (where actual error message is)
$logFile = __DIR__ . '/core/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    // Get last 10000 chars
    $tail = substr($content, -10000);
    // Find last ERROR entry
    $pos = strrpos($tail, 'production.ERROR');
    if ($pos === false) $pos = strrpos($tail, 'local.ERROR');
    if ($pos !== false) {
        $errorChunk = substr($tail, $pos, 2000);
        echo "<h2>Latest Error:</h2><pre style='background:#fff0f0;padding:10px;font-size:12px'>" . htmlspecialchars($errorChunk) . "</pre>";
    }
}
