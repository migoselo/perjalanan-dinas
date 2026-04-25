<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    $request = \Illuminate\Http\Request::capture()
);

try {
    echo "Testing Google Client initialization...\n";
    
    $credentialsPath = storage_path('app/google.json');
    echo "Credentials path: " . $credentialsPath . "\n";
    echo "File exists: " . (file_exists($credentialsPath) ? 'YES' : 'NO') . "\n";
    
    // Check file content
    $json = file_get_contents($credentialsPath);
    $data = json_decode($json, true);
    echo "JSON decoded successfully: " . ($data ? 'YES' : 'NO') . "\n";
    
    if ($data) {
        echo "Type: " . ($data['type'] ?? 'N/A') . "\n";
        echo "Project ID: " . ($data['project_id'] ?? 'N/A') . "\n";
        echo "Client email: " . ($data['client_email'] ?? 'N/A') . "\n";
    }
    
    // Try to initialize Google Client
    echo "\nInitializing Google Client...\n";
    $client = new \Google\Client();
    $client->setAuthConfig($credentialsPath);
    $client->addScope(\Google\Service\Drive::DRIVE);
    $client->setApplicationName('Perjalanan Dinas System');
    
    echo "Google Client initialized successfully!\n";
    
    // Try to create Drive Service
    echo "Creating Drive Service...\n";
    $driveService = new \Google\Service\Drive($client);
    echo "Drive Service created successfully!\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
