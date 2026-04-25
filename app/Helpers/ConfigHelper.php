<?php

namespace App\Helpers;

class ConfigHelper
{
    /**
     * Setup SSL certificate path di awal aplikasi
     * Pastikan semua curl & openssl gunakan path yang benar
     */
    public static function setupSSLConfiguration()
    {
        // FORCE: Ensure cacert ada di storage first
        $caCertPath = SSLHelper::getCACertPath();
        
        if (!$caCertPath || !file_exists($caCertPath)) {
            \Log::warning('SSL Certificate not found after setup attempt', [
                'attempted_path' => $caCertPath
            ]);
            return false;
        }
        
        // Verify certificate is valid
        if (filesize($caCertPath) < 10000) {
            \Log::error('SSL Certificate is too small (corrupt?)', [
                'path' => $caCertPath,
                'size' => filesize($caCertPath),
            ]);
            return false;
        }
        
        // ⭐ FORCE: Override ini settings at runtime (strongest method)
        @ini_set('curl.cainfo', $caCertPath);
        @ini_set('openssl.cafile', $caCertPath);
        
        // Set environment variables untuk curl & OpenSSL
        putenv('CURL_CA_BUNDLE=' . $caCertPath);
        putenv('SSL_CERT_FILE=' . $caCertPath);
        putenv('SSL_CERT_DIR=' . dirname($caCertPath));
        
        // Set PHP streaming context untuk file_get_contents(), stream_*, dll
        stream_context_set_default([
            'ssl' => [
                'cafile' => $caCertPath,
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false,
            ],
            'http' => [
                'timeout' => 30,
            ],
            'https' => [
                'timeout' => 30,
            ]
        ]);
        
        \Log::info('SSL Configuration setup completed successfully', [
            'ca_cert_path' => $caCertPath,
            'file_exists' => file_exists($caCertPath),
            'file_size' => filesize($caCertPath),
            'is_readable' => is_readable($caCertPath),
            'curl.cainfo_override' => ini_get('curl.cainfo'),
            'openssl.cafile_override' => ini_get('openssl.cafile'),
        ]);
        
        return true;
    }
    
    /**
     * Verify PHP curl extension is properly configured
     */
    public static function verifyCurlConfiguration()
    {
        if (!extension_loaded('curl')) {
            throw new \Exception('cURL extension not loaded');
        }
        
        $curlVersion = curl_version();
        
        $info = [
            'curl_version' => $curlVersion['version'],
            'openssl_version' => $curlVersion['ssl_version'],
            'current_ca_bundle' => ini_get('curl.cainfo') ?: 'Not set',
            'environment_ca_bundle' => getenv('CURL_CA_BUNDLE') ?: 'Not set',
        ];
        
        \Log::debug('cURL Configuration', $info);
        
        return $info;
    }
}
