<?php

// Simple test to verify GoogleDriveService works
require 'vendor/autoload.php';

try {
    echo "1. Testing Google Client instantiation...\n";
    $client = new Google\Client();
    echo "   ✓ Google\Client OK\n";
    
    echo "\n2. Testing GuzzleHttp...\n";
    $http = new GuzzleHttp\Client();
    echo "   ✓ GuzzleHttp\Client OK\n";
    
    echo "\n3. Testing GoogleDriveService instantiation...\n";
    $service = new App\Services\GoogleDriveService();
    echo "   ✓ GoogleDriveService instantiated\n";
    
    echo "\n✓ All tests passed!\n";
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
