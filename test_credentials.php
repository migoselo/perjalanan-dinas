<?php

require 'vendor/autoload.php';

// Check if file exists
$credPath = 'storage/app/google.json';
$absolutePath = __DIR__ . '/' . $credPath;

echo "Credential Path: " . $credPath . "\n";
echo "Absolute Path: " . $absolutePath . "\n";
echo "File exists: " . (file_exists($absolutePath) ? 'YES' : 'NO') . "\n";

if (file_exists($absolutePath)) {
    $content = file_get_contents($absolutePath);
    $json = json_decode($content, true);
    echo "JSON valid: " . ($json ? 'YES' : 'NO') . "\n";
    if ($json) {
        echo "Type: " . $json['type'] . "\n";
        echo "Project ID: " . $json['project_id'] . "\n";
    }
}

// Test Google Client
try {
    $client = new Google\Client();
    echo "\nGoogle Client instantiated: YES\n";
    
    // Test setting auth config
    if (file_exists($absolutePath)) {
        $client->setAuthConfig($absolutePath);
        echo "Auth config set: YES\n";
    }
    
    // Check if Drive service class exists
    echo "Google\\Service\\Drive exists: " . (class_exists('Google\Service\Drive') ? 'YES' : 'NO') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
