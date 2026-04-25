<?php
echo "===== PHP CONFIGURATION CHECK =====\n\n";

echo "PHP Loaded INI: " . php_ini_loaded_file() . "\n";
echo "Loaded INI Scanned: " . php_ini_scanned_files() . "\n\n";

echo "===== CURL CONFIGURATION =====\n";
$curl_cainfo = ini_get('curl.cainfo');
echo "curl.cainfo: " . ($curl_cainfo ?: 'NOT SET') . "\n";

$openssl_cafile = ini_get('openssl.cafile');
echo "openssl.cafile: " . ($openssl_cafile ?: 'NOT SET') . "\n\n";

echo "===== CURL INFO =====\n";
$curlVersion = curl_version();
echo "cURL Version: " . $curlVersion['version'] . "\n";
echo "SSL Version: " . $curlVersion['ssl_version'] . "\n\n";

echo "===== ENVIRONMENT VARIABLES =====\n";
echo "CURL_CA_BUNDLE: " . (getenv('CURL_CA_BUNDLE') ?: 'NOT SET') . "\n";
echo "SSL_CERT_FILE: " . (getenv('SSL_CERT_FILE') ?: 'NOT SET') . "\n\n";

echo "===== PHPINIScan ALL =====\n";
$inis = php_ini_scanned_files();
if ($inis) {
    echo "Additional INI files:\n";
    foreach (explode(",", $inis) as $ini) {
        $ini = trim($ini);
        if (file_exists($ini)) {
            echo "  ✓ " . $ini . "\n";
            // Check if this file has curl.cainfo
            $content = file_get_contents($ini);
            if (strpos($content, 'curl.cainfo') !== false) {
                $lines = file($ini);
                foreach ($lines as $lineNum => $line) {
                    if (strpos($line, 'curl.cainfo') !== false) {
                        echo "    Line " . ($lineNum+1) . ": " . trim($line) . "\n";
                    }
                }
            }
        }
    }
}
?>
