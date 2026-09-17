<?php
// Debug script - visit: https://namartzone.store/debug_routes.php

require __DIR__.'/core/vendor/autoload.php';
$app = require_once __DIR__.'/core/bootstrap/app.php';

// Bootstrap without handling HTTP request
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

header('Content-Type: text/plain');

echo "=== ROUTE & URL DEBUG ===\n\n";

echo "APP_URL (config): " . config('app.url') . "\n";
echo "APP_URL (env): " . env('APP_URL') . "\n\n";

try {
    echo "route('front.index') = " . route('front.index') . "\n";
    echo "url('/') = " . url('/') . "\n\n";
} catch (Exception $e) {
    echo "Route error: " . $e->getMessage() . "\n\n";
}

// Check categories
try {
    $home_customize = DB::table('home_cutomizes')->first();
    echo "=== FEATURE CATEGORIES ===\n";
    if ($home_customize && $home_customize->feature_category) {
        $fc = json_decode($home_customize->feature_category, true);
        for ($i = 1; $i <= 4; $i++) {
            $catId = $fc['category_id' . $i] ?? null;
            if ($catId) {
                $cat = DB::table('categories')->find($catId);
                echo "Category $i (ID:$catId): " . ($cat ? $cat->name : "*** MISSING - CAUSES 404! ***") . "\n";
            }
        }
    }

    echo "\n=== POPULAR CATEGORIES ===\n";
    if ($home_customize && $home_customize->popular_category) {
        $pc = json_decode($home_customize->popular_category, true);
        for ($i = 1; $i <= 4; $i++) {
            $catId = $pc['category_id' . $i] ?? null;
            if ($catId) {
                $cat = DB::table('categories')->find($catId);
                echo "Category $i (ID:$catId): " . ($cat ? $cat->name : "*** MISSING - CAUSES 404! ***") . "\n";
            }
        }
    }

    echo "\n=== MENU DATA ===\n";
    $menu = DB::table('menus')->find(1);
    if ($menu) {
        $links = json_decode($menu->menus, true);
        foreach ($links as $link) {
            echo "Menu: " . $link['text'] . " | type: " . $link['type'] . " | href: '" . $link['href'] . "'\n";
        }
    }

    echo "\n=== SERVER .ENV FILE ===\n";
    $envPath = __DIR__ . '/core/.env';
    if (file_exists($envPath)) {
        $envContent = file_get_contents($envPath);
        // Only show APP_URL and DB lines
        foreach (explode("\n", $envContent) as $line) {
            if (strpos($line, 'APP_URL') !== false || strpos($line, 'APP_ENV') !== false || strpos($line, 'APP_DEBUG') !== false) {
                echo $line . "\n";
            }
        }
    }

    echo "\n=== SETTINGS ===\n";
    $setting = DB::table('settings')->find(1);
    if ($setting) {
        echo "Theme: " . ($setting->theme ?? 'N/A') . "\n";
    }

} catch (Exception $e) {
    echo "\n*** ERROR: " . $e->getMessage() . " ***\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== DONE ===\n";
