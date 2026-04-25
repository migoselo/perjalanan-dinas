<?php
require 'vendor/autoload.php';

// Test 1: Check if file exists
echo "File exists: " . (file_exists('app/Http/Controllers/SpbyController.php') ? "YES" : "NO") . "\n";

// Test 2: Check if class can be loaded
echo "Class exists: " . (class_exists('App\Http\Controllers\SpbyController') ? "YES" : "NO") . "\n";

// Test 3: Check autoload classmap
$map = include 'vendor/composer/autoload_classmap.php';
$spbyKey = array_search('app/Http/Controllers/SpbyController.php', $map);
echo "In classmap: " . ($spbyKey ? "YES ($spbyKey)" : "NO") . "\n";

// Test 4: Check PSR-4
$psr4 = include 'vendor/composer/autoload_psr4.php';
echo "\nPSR-4 namespaces:\n";
foreach ($psr4 as $ns => $dirs) {
    echo "  $ns => " . json_encode($dirs) . "\n";
}

// Test 5: Direct require
echo "\nTrying direct require...\n";
try {
    require_once 'app/Http/Controllers/SpbyController.php';
    echo "Direct require: OK\n";
    echo "Class exists now: " . (class_exists('App\Http\Controllers\SpbyController') ? "YES" : "NO") . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
