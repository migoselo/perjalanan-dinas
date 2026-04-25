<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\ConfigHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        require_once app_path('Helpers/NumberHelper.php');
        
        // ⭐ FORCE: Set SSL environment variables FIRST thing on boot
        // This overrides any system settings
        $caCertPath = storage_path('app/cacert.pem');
        if (file_exists($caCertPath)) {
            putenv('CURL_CA_BUNDLE=' . $caCertPath);
            putenv('SSL_CERT_FILE=' . $caCertPath);
            putenv('SSL_CERT_DIR=' . dirname($caCertPath));
            
            \Log::debug('AppServiceProvider: SSL environment variables set', [
                'CURL_CA_BUNDLE' => getenv('CURL_CA_BUNDLE'),
                'SSL_CERT_FILE' => getenv('SSL_CERT_FILE'),
            ]);
        }
        
        // Setup SSL configuration untuk Google Drive & HTTPS requests
        ConfigHelper::setupSSLConfiguration();
    }
}
