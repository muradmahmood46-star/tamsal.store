<?php
// Simple debug - no Laravel loading
header('Content-Type: text/plain');
echo "=== DEBUG INFO ===\n\n";

echo "Script location: " . __FILE__ . "\n";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Server name: " . $_SERVER['SERVER_NAME'] . "\n";
echo "PHP version: " . phpversion() . "\n\n";

// Check .env
echo "=== .ENV FILE ===\n";
$envPath = __DIR__ . '/core/.env';
if (file_exists($envPath)) {
    $lines = file($envPath);
    foreach ($lines as $line) {
        $line = trim($line);
        if (strpos($line, 'APP_URL') !== false || 
            strpos($line, 'APP_ENV') !== false || 
            strpos($line, 'APP_DEBUG') !== false ||
            strpos($line, 'DB_DATABASE') !== false) {
            echo $line . "\n";
        }
    }
} else {
    echo "*** .env NOT FOUND at: $envPath ***\n";
}

// Check .htaccess
echo "\n=== .HTACCESS ===\n";
$htaccess = __DIR__ . '/.htaccess';
if (file_exists($htaccess)) {
    echo file_get_contents($htaccess);
} else {
    echo "*** .htaccess NOT FOUND ***\n";
}

// List files in root
echo "\n=== ROOT FILES ===\n";
$files = scandir(__DIR__);
foreach ($files as $f) {
    if ($f != '.' && $f != '..') {
        echo (is_dir(__DIR__.'/'.$f) ? '[DIR] ' : '[FILE] ') . $f . "\n";
    }
}

// Check key files exist
echo "\n=== KEY FILES CHECK ===\n";
$check = [
    'index.php',
    'clear.php',
    'debug_routes.php',
    'core/routes/web.php',
    'core/vendor/autoload.php',
    'core/bootstrap/app.php',
];
foreach ($check as $f) {
    echo $f . ": " . (file_exists(__DIR__.'/'.$f) ? "EXISTS" : "*** MISSING ***") . "\n";
}

echo "\n=== DONE ===\n";
