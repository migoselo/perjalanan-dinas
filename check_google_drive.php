<?php

/**
 * Google Drive Upload Debug & Test Script
 * 
 * Cara pakai:
 * 1. Paste ke `check_data.php` atau buat file baru di root project
 * 2. Jalankan: php check_google_drive.php
 * 3. Lihat output untuk diagnose masalah
 */

echo "=== Google Drive Upload Configuration Check ===\n\n";

// Include Laravel bootstrap
require_once(__DIR__ . '/bootstrap/app.php');

try {
    // Test 1: Check Environment Variables
    echo "1. Environment Variables Check\n";
    echo "   GOOGLE_DRIVE_CREDENTIALS_PATH: " . (env('GOOGLE_DRIVE_CREDENTIALS_PATH') ? '✓ SET' : '✗ NOT SET') . "\n";
    echo "   GOOGLE_DRIVE_ROOT_FOLDER_ID: " . (env('GOOGLE_DRIVE_ROOT_FOLDER_ID') ? '✓ SET' : '✗ NOT SET') . "\n";
    echo "   GOOGLE_DRIVE_CREDENTIALS_JSON: " . (env('GOOGLE_DRIVE_CREDENTIALS_JSON') ? '✓ SET (length: ' . strlen(env('GOOGLE_DRIVE_CREDENTIALS_JSON')) . ')' : '✗ NOT SET') . "\n\n";

    // Test 2: Check Credentials File
    echo "2. Credentials File Check\n";
    $credPath = env('GOOGLE_DRIVE_CREDENTIALS_PATH');
    if ($credPath && file_exists($credPath)) {
        echo "   File exists: ✓\n";
        echo "   Path: {$credPath}\n";
        echo "   Size: " . filesize($credPath) . " bytes\n";
        
        $json = json_decode(file_get_contents($credPath), true);
        if ($json) {
            echo "   Valid JSON: ✓\n";
            echo "   Type: " . ($json['type'] ?? 'N/A') . "\n";
            echo "   Email: " . ($json['client_email'] ?? 'N/A') . "\n";
        } else {
            echo "   Valid JSON: ✗ (Invalid JSON format)\n";
        }
    } else {
        echo "   File exists: ✗\n";
        echo "   Expected path: {$credPath}\n";
    }
    echo "\n";

    // Test 3: Check Storage Configuration
    echo "3. Storage Configuration Check\n";
    $config = config('filesystems.disks.google');
    if ($config) {
        echo "   Google disk config: ✓ SET\n";
        echo "   Driver: " . ($config['driver'] ?? 'N/A') . "\n";
    } else {
        echo "   Google disk config: ✗ NOT CONFIGURED\n";
    }
    echo "\n";

    // Test 4: Check Google Drive Service
    echo "4. Google Drive Service Check\n";
    try {
        $googleDrive = new \App\Services\GoogleDriveService();
        echo "   Service initialization: ✓ SUCCESS\n";
        
        // Try to get root folder info
        echo "   Root Folder ID: " . env('GOOGLE_DRIVE_ROOT_FOLDER_ID') . "\n";
        
    } catch (\Exception $e) {
        echo "   Service initialization: ✗ FAILED\n";
        echo "   Error: " . $e->getMessage() . "\n";
    }
    echo "\n";

    // Test 5: Check Database Schema
    echo "5. Database Schema Check\n";
    $table = \DB::table('information_schema.COLUMNS')
        ->where('TABLE_NAME', 'spt_progres')
        ->where('TABLE_SCHEMA', env('DB_DATABASE'))
        ->get(['COLUMN_NAME'])
        ->pluck('COLUMN_NAME')
        ->toArray();
    
    if ($table) {
        echo "   Table exists: ✓\n";
        $requiredCols = [
            'google_drive_folder_id',
            'laporan_file_id',
            'penanggung_jawab_file_id',
            'pembayaran_file_id',
        ];
        foreach ($requiredCols as $col) {
            $status = in_array($col, $table) ? '✓' : '✗';
            echo "   - {$col}: {$status}\n";
        }
    } else {
        echo "   Table exists: ✗\n";
    }
    echo "\n";

    // Test 6: Check SPT Data
    echo "6. SPT Progres Data Check\n";
    $sptCount = \App\Models\SPTProgres::count();
    echo "   Total SPT records: {$sptCount}\n";
    
    if ($sptCount > 0) {
        $spt = \App\Models\SPTProgres::first();
        echo "   First SPT ID: " . $spt->id . "\n";
        echo "   Folder ID: " . ($spt->google_drive_folder_id ?? '✗ NULL') . "\n";
        echo "   Laporan File ID: " . ($spt->laporan_file_id ?? '✗ NULL') . "\n";
        echo "   Complete: " . ($spt->is_complete ? '✓ YES' : '✗ NO') . "\n";
    }
    echo "\n";

    // Test 7: Test File Storage Paths
    echo "7. File Storage Check\n";
    $uploadPath = storage_path('app/uploads/spt');
    if (is_dir($uploadPath)) {
        echo "   Upload directory exists: ✓\n";
        echo "   Path: {$uploadPath}\n";
        $files = glob($uploadPath . '/*');
        echo "   Files in directory: " . count($files) . "\n";
    } else {
        echo "   Upload directory exists: ✗\n";
        echo "   Creating directory...\n";
        mkdir($uploadPath, 0755, true);
        echo "   Created: ✓\n";
    }
    echo "\n";

    echo "=== Check Complete ===\n";
    echo "\nNext Steps:\n";
    echo "1. If all checks pass, try uploading a file\n";
    echo "2. Check storage/logs/laravel.log for detailed errors\n";
    echo "3. Check Google Drive for new files in the root folder\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
