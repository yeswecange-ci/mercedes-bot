#!/usr/bin/env php
<?php

echo "=================================\n";
echo "Vite Asset Diagnostic Script\n";
echo "=================================\n\n";

// Check if we're in the Laravel directory
if (!file_exists('artisan')) {
    echo "ERROR: This script must be run from the Laravel root directory\n";
    exit(1);
}

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "1. Environment Check:\n";
echo "   APP_ENV: " . config('app.env') . "\n";
echo "   APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "\n";
echo "   APP_URL: " . config('app.url') . "\n";
echo "   ASSET_URL: " . (config('app.asset_url') ?: 'not set') . "\n\n";

echo "2. Manifest File Check:\n";
$manifestPath = public_path('build/manifest.json');
if (file_exists($manifestPath)) {
    echo "   ✓ Manifest exists at: {$manifestPath}\n";
    $manifest = json_decode(file_get_contents($manifestPath), true);
    echo "   Manifest contents:\n";
    print_r($manifest);
} else {
    echo "   ✗ Manifest NOT found at: {$manifestPath}\n";
}
echo "\n";

echo "3. Asset Files Check:\n";
$buildDir = public_path('build');
if (is_dir($buildDir)) {
    echo "   ✓ Build directory exists\n";
    $files = glob($buildDir . '/assets/*');
    echo "   Assets found:\n";
    foreach ($files as $file) {
        $size = filesize($file);
        echo "   - " . basename($file) . " (" . round($size/1024, 2) . " KB)\n";
    }
} else {
    echo "   ✗ Build directory NOT found\n";
}
echo "\n";

echo "4. Vite Helper Test:\n";
try {
    $vite = app(\Illuminate\Foundation\Vite::class);
    echo "   ✓ Vite helper is available\n";

    // Try to generate asset URLs
    ob_start();
    echo Vite::asset('resources/css/app.css');
    $cssOutput = ob_get_clean();

    ob_start();
    echo Vite::asset('resources/js/app.js');
    $jsOutput = ob_get_clean();

    echo "   CSS Output:\n";
    echo "   " . trim($cssOutput) . "\n\n";
    echo "   JS Output:\n";
    echo "   " . trim($jsOutput) . "\n";
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "5. Public URL Test:\n";
$appUrl = config('app.url');
echo "   CSS should be at: {$appUrl}/build/assets/app-fN596C0N.css\n";
echo "   JS should be at: {$appUrl}/build/assets/app-kGY04szw.js\n\n";

echo "=================================\n";
echo "Diagnostic Complete!\n";
echo "=================================\n";
