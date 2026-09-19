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
    echo "<p style='color:green'>✅ photo column added successfully!</p>";
} else {
    echo "<p style='color:blue'>ℹ️ photo column already exists.</p>";
}

// Check latest laravel log
$logFile = __DIR__ . '/core/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = array_slice(file($logFile), -50);
    echo "<h2>Last 50 log lines:</h2><pre style='font-size:11px;background:#f5f5f5;padding:10px'>";
    foreach ($lines as $line) {
        if (strpos($line, 'ERROR') !== false || strpos($line, 'Exception') !== false) {
            echo "<span style='color:red'>" . htmlspecialchars($line) . "</span>";
        } else {
            echo htmlspecialchars($line);
        }
    }
    echo "</pre>";
}
