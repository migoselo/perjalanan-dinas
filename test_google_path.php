<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    $request = \Illuminate\Http\Request::capture()
);

$path = 'storage/app/google.json';
echo "Original path: " . $path . "\n";
echo "file_exists(path): " . (file_exists($path) ? 'YES' : 'NO') . "\n";

$full = base_path($path);
echo "base_path(path): " . $full . "\n";
echo "file_exists(base_path): " . (file_exists($full) ? 'YES' : 'NO') . "\n";

$storage = storage_path('app/google.json');
echo "storage_path('app/google.json'): " . $storage . "\n";
echo "file_exists(storage_path): " . (file_exists($storage) ? 'YES' : 'NO') . "\n";

// Test config
echo "\nConfig values:\n";
echo "credentials_path: " . config('services.google.drive.credentials_path') . "\n";
echo "root_folder_id: " . config('services.google.drive.root_folder_id') . "\n";

// Test credentials_json
$credJson = config('services.google.drive.credentials_json');
echo "Has credentials_json: " . ($credJson ? 'YES' : 'NO') . "\n";
