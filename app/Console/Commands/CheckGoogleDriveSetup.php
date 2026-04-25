<?php

namespace App\Console\Commands;

use App\Services\GoogleDriveHelper;
use Illuminate\Console\Command;

class CheckGoogleDriveSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google-drive:check-setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Google Drive configuration dan credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Google Drive Setup Check ===');
        $this->newLine();

        // Check credentials
        $this->info('Checking credentials configuration...');
        $validation = GoogleDriveHelper::validateCredentials();

        if ($validation['valid']) {
            $this->components->task('Credentials configuration', fn () => true);
        } else {
            $this->components->task('Credentials configuration', fn () => false);
            foreach ($validation['errors'] as $error) {
                $this->error('  ✗ ' . $error);
            }
            return 1;
        }

        // Check environment variables
        $this->newLine();
        $this->info('Environment variables:');
        
        $rootFolderId = config('services.google.drive.root_folder_id');
        if ($rootFolderId) {
            $this->line('  ✓ GOOGLE_DRIVE_ROOT_FOLDER_ID: ' . substr($rootFolderId, 0, 10) . '...');
        } else {
            $this->error('  ✗ GOOGLE_DRIVE_ROOT_FOLDER_ID not set');
            return 1;
        }

        $credentialsPath = config('services.google.drive.credentials_path');
        if ($credentialsPath) {
            $this->line('  ✓ GOOGLE_DRIVE_CREDENTIALS_PATH: ' . $credentialsPath);
        } else {
            $this->line('  ℹ GOOGLE_DRIVE_CREDENTIALS_PATH not set (using JSON)');
        }

        $credentialsJson = config('services.google.drive.credentials_json');
        if ($credentialsJson) {
            $this->line('  ✓ GOOGLE_DRIVE_CREDENTIALS_JSON: set');
        } else if (!$credentialsPath) {
            $this->error('  ✗ Neither CREDENTIALS_PATH nor CREDENTIALS_JSON is set');
            return 1;
        }

        // Test connection
        $this->newLine();
        $this->info('Testing Google Drive connection...');
        
        try {
            $service = new \App\Services\GoogleDriveService();
            $this->components->task('Google Drive API connection', fn () => true);
            $this->info('✓ Successfully connected to Google Drive API');
        } catch (\Exception $e) {
            $this->components->task('Google Drive API connection', fn () => false);
            $this->error('✗ Connection failed: ' . $e->getMessage());
            return 1;
        }

        // Display setup instructions
        $this->newLine();
        $this->info('=== Setup Status ===');
        $this->line('All checks passed! Google Drive setup is ready.');
        
        $this->newLine();
        $this->info('Next steps:');
        $this->line('1. Run migration: php artisan migrate');
        $this->line('2. Access progres page at: /progres');
        $this->line('3. Start adding SPT entries');

        return 0;
    }
}
