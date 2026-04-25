<?php

namespace App\Helpers;

class SSLHelper
{
    private static $cacertPath = null;

    /**
     * PRIORITIZE storage path - download jika belum ada
     */
    public static function getCACertPath()
    {
        // Gunakan cache jika sudah di-hit sebelumnya
        if (self::$cacertPath !== null && file_exists(self::$cacertPath)) {
            return self::$cacertPath;
        }

        // PRIORITY 1: Force ensure storage path ada
        $storagePath = self::ensureCertificateInStorage();
        if ($storagePath && file_exists($storagePath) && filesize($storagePath) > 1000) {
            self::$cacertPath = $storagePath;
            \Log::debug('Using storage cacert.pem', ['path' => $storagePath]);
            return $storagePath;
        }

        // PRIORITY 2: Cek path sistem yang sudah ditetapkan di php.ini
        $systemPaths = [
            'C:\laragon\etc\ssl\cacert.pem',
            ini_get('curl.cainfo') ?: null,
            ini_get('openssl.cafile') ?: null,
        ];

        foreach ($systemPaths as $path) {
            if ($path && file_exists($path) && filesize($path) > 1000) {
                $normalized = str_replace('\\', '/', $path);
                self::$cacertPath = $normalized;
                \Log::debug('Using system cacert.pem', ['path' => $normalized]);
                return $normalized;
            }
        }

        // PRIORITY 3: Fallback ke Guzzle bundled
        try {
            $composerCertPath = \Composer\CaBundle\CaBundle::getBundledCaBundlePath();
            if (file_exists($composerCertPath)) {
                \Log::info('Using Composer bundled CA certificate', ['path' => $composerCertPath]);
                self::$cacertPath = $composerCertPath;
                return $composerCertPath;
            }
        } catch (\Exception $e) {
            \Log::debug('Composer bundled certificate not available');
        }

        \Log::error('CRITICAL: No valid CA certificate found!');
        return false;
    }

    /**
     * AGGRESSIVE: Ensure cacert ada di storage
     */
    private static function ensureCertificateInStorage()
    {
        $storagePath = storage_path('app/cacert.pem');
        $storagePath = str_replace('\\', '/', $storagePath);
        $storageDir = dirname($storagePath);

        // Create directory
        if (!is_dir($storageDir)) {
            @mkdir($storageDir, 0755, true);
        }

        // Check if file sudah ada dan valid
        if (file_exists($storagePath) && filesize($storagePath) > 10000) {
            \Log::debug('Storage cacert.pem already exists', [
                'path' => $storagePath,
                'size' => filesize($storagePath),
            ]);
            return $storagePath;
        }

        // Force download dari 2 URL
        $urls = [
            'https://curl.se/ca/cacert.pem',
            'https://raw.githubusercontent.com/curl/curl/master/lib/ca-bundle.crt',
            'https://www.mozilla.org/media/security/servefiles/sha256/2024-01-09-12-26-11/cacert.pem',
        ];

        foreach ($urls as $url) {
            \Log::info('Attempting to download CA certificate', ['url' => $url]);
            
            $certData = self::downloadCertificate($url);
            
            if ($certData && strlen($certData) > 10000) {
                \Log::info('Successfully downloaded CA certificate', [
                    'url' => $url,
                    'size' => strlen($certData),
                ]);
                
                // Write ke storage
                $written = file_put_contents($storagePath, $certData);
                if ($written !== false) {
                    chmod($storagePath, 0644);
                    \Log::info('CA Certificate saved to storage', [
                        'path' => $storagePath,
                        'bytes_written' => $written,
                    ]);
                    return $storagePath;
                }
            }
        }

        \Log::warning('Failed to download CA certificate from all sources');
        return null;
    }

    /**
     * Download certificate dengan timeout dan error handling
     */
    private static function downloadCertificate($url)
    {
        try {
            // Gunakan stream context dengan verify_peer = false untuk download cert itu sendiri
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'user_agent' => 'Laravel/GoogleDrive',
                    'follow_location' => true,
                    'max_redirects' => 5,
                ],
                'https' => [
                    'timeout' => 30,
                    'verify_peer' => false, // Temporary disable untuk download cert
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ]
            ]);

            @ini_set('default_socket_timeout', 30);
            $certData = @file_get_contents($url, false, $context);
            
            return $certData;
        } catch (\Exception $e) {
            \Log::debug('Failed to download from URL', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get Guzzle client options dengan SSL configuration yang tepat
     */
    public static function getGuzzleClientOptions($extraOptions = [])
    {
        $caCertPath = self::getCACertPath();

        $options = [
            'verify' => $caCertPath !== false ? $caCertPath : false,
            'timeout' => 60,
            'http_errors' => false,
            'connect_timeout' => 30,
        ];

        return array_merge($options, $extraOptions);
    }
}
