<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleDriveService;
use App\Helpers\SSLHelper;

class TestGoogleDrive extends Command
{
    protected $signature = 'test:google-drive';
    protected $description = 'Test Google Drive connection and SSL configuration';

    public function handle()
    {
        $this->info('=== Google Drive & SSL Certificate Test ===');
        
        // Test 1: SSL Certificate
        $this->info("\n[TEST 1] SSL Certificate Configuration");
        $this->testSSLCertificate();
        
        // Test 2: PHP INI Settings
        $this->info("\n[TEST 2] PHP INI Configuration");
        $this->testPHPConfiguration();
        
        // Test 3: Google Credentials File
        $this->info("\n[TEST 3] Google Credentials File");
        $this->testGoogleCredentials();
        
        // Test 4: Google Drive Service
        $this->info("\n[TEST 4] Google Drive Service");
        $this->testGoogleDriveService();
    }
    
    private function testSSLCertificate()
    {
        try {
            $certPath = SSLHelper::getCACertPath();
            $this->line("Certificate path: {$certPath}");
            
            if (file_exists($certPath)) {
                $size = filesize($certPath);
                $this->line("✓ File exists");
                $this->line("  Size: {$size} bytes");
                $this->line("  Readable: " . (is_readable($certPath) ? 'YES' : 'NO'));
                
                if ($size < 10000) {
                    $this->warn("  WARNING: Certificate file is too small!");
                }
            } else {
                $this->error("✗ File does NOT exist");
                return;
            }
        } catch (\Exception $e) {
            $this->error("✗ Error: " . $e->getMessage());
        }
    }
    
    private function testPHPConfiguration()
    {
        $phpIniPath = php_ini_loaded_file();
        $this->line("PHP INI: {$phpIniPath}");
        
        $curlCainfo = ini_get('curl.cainfo');
        $opensslCafile = ini_get('openssl.cafile');
        
        $this->line("curl.cainfo: {$curlCainfo}");
        if ($curlCainfo && file_exists($curlCainfo)) {
            $this->line("  ✓ File exists (" . filesize($curlCainfo) . " bytes)");
        } else {
            $this->error("  ✗ File not found!");
        }
        
        $this->line("openssl.cafile: {$opensslCafile}");
        if ($opensslCafile && file_exists($opensslCafile)) {
            $this->line("  ✓ File exists (" . filesize($opensslCafile) . " bytes)");
        } else {
            $this->error("  ✗ File not found!");
        }
    }
    
    private function testGoogleCredentials()
    {
        $credPath = config('services.google.drive.credentials_path');
        $this->line("Config path: {$credPath}");
        
        if ($credPath && !file_exists($credPath)) {
            $credPath = storage_path($credPath);
            $this->line("Resolved to storage: {$credPath}");
        }
        
        if (file_exists($credPath)) {
            $this->line("✓ Credentials file found");
            $this->line("  Path: {$credPath}");
            $this->line("  Size: " . filesize($credPath) . " bytes");
        } else {
            $this->error("✗ Credentials file NOT found at: {$credPath}");
        }
        
        $rootFolder = config('services.google.drive.root_folder_id');
        $this->line("Root folder ID configured: " . ($rootFolder ? '✓ YES' : '✗ NO'));
    }
    
    private function testGoogleDriveService()
    {
        try {
            $this->line("Initializing GoogleDriveService...");
            $service = new GoogleDriveService();
            $this->line("✓ Service initialized successfully");
            
            $this->line("\nTesting folder check...");
            $result = $service->checkFolderExists('TEST_FOLDER_CHECK');
            $this->line("✓ Folder check successful");
            $this->line("  Result: " . ($result['exists'] ? 'Folder exists' : 'Folder does not exist'));
            
        } catch (\Exception $e) {
            $this->error("✗ Error: " . $e->getMessage());
            $this->error("\nFull error trace:");
            $this->line($e->getTraceAsString());
        }
    }
}
