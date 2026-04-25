<?php
/**
 * CSRF Token Verification Test
 * 
 * Jalankan: php csrf_test.php
 */

require_once(__DIR__ . '/bootstrap/app.php');

echo "=== CSRF Token Configuration Check ===\n\n";

try {
    // Test 1: Check Session Configuration
    echo "1. Session Configuration\n";
    $sessionDriver = config('session.driver');
    $sessionLifetime = config('session.lifetime');
    echo "   Driver: {$sessionDriver}\n";
    echo "   Lifetime: {$sessionLifetime} minutes\n";
    echo "   Cookie name: " . config('session.cookie') . "\n";
    echo "   Secure: " . (config('session.secure') ? 'Yes' : 'No') . "\n";
    echo "   Http Only: " . (config('session.http_only') ? 'Yes' : 'No') . "\n\n";

    // Test 2: Check Middleware
    echo "2. Middleware Configuration\n";
    $middlewareGroups = config('app.middleware_groups');
    if (isset($middlewareGroups['web'])) {
        echo "   Web middleware group:\n";
        foreach ($middlewareGroups['web'] as $middleware) {
            $csrfActive = strpos($middleware, 'Csrf') !== false ? '✓' : ' ';
            echo "   [{$csrfActive}] {$middleware}\n";
        }
    } else {
        echo "   Using Laravel default middleware\n";
    }
    echo "\n";

    // Test 3: Check CSRF Exception Routes
    echo "3. CSRF Exception Routes\n";
    $csrfExceptions = config('csrf.except', []);
    if (!empty($csrfExceptions)) {
        echo "   Routes excluded from CSRF:\n";
        foreach ($csrfExceptions as $exception) {
            echo "   - {$exception}\n";
        }
    } else {
        echo "   No routes excluded from CSRF\n";
    }
    echo "\n";

    // Test 4: Check Upload Route
    echo "4. Upload Route Configuration\n";
    $uploadRoute = '/progres/{id}/upload';
    $isApiRoute = strpos($uploadRoute, 'api') !== false;
    echo "   Route: {$uploadRoute}\n";
    echo "   Is API route: " . ($isApiRoute ? 'Yes' : 'No - CSRF should apply') . "\n";
    echo "   CSRF Protection: " . (!$isApiRoute ? '✓ SHOULD BE ACTIVE' : '✗ Not protected') . "\n\n";

    // Test 5: Session storage
    echo "5. Session Storage\n";
    $sessionPath = storage_path('framework/sessions');
    if ($sessionDriver === 'file') {
        echo "   Driver: File-based\n";
        echo "   Path: {$sessionPath}\n";
        echo "   Path exists: " . (is_dir($sessionPath) ? '✓ Yes' : '✗ No') . "\n";
        
        if (is_dir($sessionPath)) {
            $sessionFiles = count(glob($sessionPath . '/*'));
            echo "   Current sessions: {$sessionFiles}\n";
        }
    } elseif ($sessionDriver === 'cookie') {
        echo "   Driver: Cookie-based (minimal protection)\n";
    } elseif ($sessionDriver === 'database') {
        echo "   Driver: Database\n";
    }
    echo "\n";

    // Test 6: Check config for CSRF
    echo "6. Laravel CSRF Config\n";
    $csrfConfig = config('session.domain');
    echo "   Session domain: " . ($csrfConfig ?? 'null (use default)') . "\n";
    echo "   Same site: " . config('session.same_site') . "\n\n";

    echo "=== Check Complete ===\n";
    echo "\nRecommendations:\n";
    echo "✓ CSRF protection should be active for POST /progres/{id}/upload\n";
    echo "✓ Form harus punya @csrf directive\n";
    echo "✓ JavaScript harus send X-CSRF-TOKEN header\n";
    echo "✓ Session driver harus dapat menjaga token state\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
