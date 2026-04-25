<?php
// Load Laravel autoloader
require __DIR__ . '/vendor/autoload.php';

echo "=== SSL Configuration Check (Web Context) ===\n\n";

echo "PHP INI Loaded: " . php_ini_loaded_file() . "\n";
echo "curl.cainfo: " . ini_get('curl.cainfo') . "\n";
echo "openssl.cafile: " . ini_get('openssl.cafile') . "\n\n";

$curlPath = ini_get('curl.cainfo');
$openSSLPath = ini_get('openssl.cafile');

if ($curlPath && file_exists($curlPath)) {
    echo "✅ curl.cainfo file EXISTS: " . filesize($curlPath) . " bytes\n";
} else {
    echo "❌ curl.cainfo file NOT FOUND or empty: " . $curlPath . "\n";
}

if ($openSSLPath && file_exists($openSSLPath)) {
    echo "✅ openssl.cafile file EXISTS: " . filesize($openSSLPath) . " bytes\n";
} else {
    echo "❌ openssl.cafile file NOT FOUND or empty: " . $openSSLPath . "\n";
}

echo "\n=== Testing Guzzle/cURL Connection ===\n";

try {
    $client = new \GuzzleHttp\Client([
        'verify' => ini_get('curl.cainfo') ?: true,
        'timeout' => 10,
    ]);
    
    $response = $client->get('https://www.google.com', [
        'headers' => [
            'User-Agent' => 'Test/1.0',
        ]
    ]);
    
    echo "✅ HTTPS Connection SUCCESS (Status: " . $response->getStatusCode() . ")\n";
} catch (\Exception $e) {
    echo "❌ HTTPS Connection FAILED:\n";
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n=== Google API Token Test ===\n";

try {
    $credentialsPath = config('services.google.drive.credentials_path');
    if ($credentialsPath && !file_exists($credentialsPath)) {
        $credentialsPath = storage_path($credentialsPath);
    }
    
    if (!file_exists($credentialsPath)) {
        echo "❌ Google credentials not found: " . $credentialsPath . "\n";
    } else {
        echo "✅ Google credentials found\n";
        echo "   Path: " . $credentialsPath . "\n";
        
        $guzzleClient = new \GuzzleHttp\Client([
            'verify' => ini_get('curl.cainfo') ?: true,
            'timeout' => 30,
        ]);
        
        $client = new \Google\Client();
        $client->setHttpClient($guzzleClient);
        $client->setAuthConfig($credentialsPath);
        $client->addScope(\Google\Service\Drive::DRIVE);
        
        $token = $client->fetchAccessTokenWithAssertion();
        
        if (isset($token['access_token'])) {
            echo "✅ Google API Token obtained successfully\n";
        } elseif (isset($token['error'])) {
            echo "❌ Google API Error: " . $token['error'] . "\n";
            echo "   Description: " . ($token['error_description'] ?? 'N/A') . "\n";
        }
    }
} catch (\Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n=== SSLHelper Check ===\n";

try {
    $certPath = \App\Helpers\SSLHelper::getCACertPath();
    echo "✅ SSLHelper certificate path: " . $certPath . "\n";
    echo "   File exists: " . (file_exists($certPath) ? 'YES' : 'NO') . "\n";
    echo "   File size: " . (file_exists($certPath) ? filesize($certPath) : 'N/A') . " bytes\n";
} catch (\Exception $e) {
    echo "❌ SSLHelper Error: " . $e->getMessage() . "\n";
}
?>
