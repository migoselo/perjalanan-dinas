<?php
/**
 * Test Upload ke Google Drive
 * 
 * Cara pakai:
 * 1. Dari folder project, jalankan: php artisan tinker
 * 2. Copy-paste code di bawah ini
 * 3. Lihat hasilnya
 */

// Bootstrap Laravel
require_once(__DIR__ . '/bootstrap/app.php');

use App\Models\SPTProgres;
use App\Services\GoogleDriveService;

echo "=== Manual Google Drive Upload Test ===\n\n";

try {
    // 1. Get first SPT record
    echo "1. Getting first SPT record...\n";
    $spt = SPTProgres::first();
    
    if (!$spt) {
        echo "   ✗ No SPT record found. Please create one first via UI.\n";
        exit(1);
    }
    
    echo "   ✓ SPT ID: {$spt->id}\n";
    echo "   ✓ Nomor SPT: {$spt->nomor_spt}\n";
    echo "   ✓ Folder ID: " . ($spt->google_drive_folder_id ?? '✗ NO FOLDER ID') . "\n\n";

    if (!$spt->google_drive_folder_id) {
        echo "   ERROR: SPT doesn't have Google Drive folder. Run store() endpoint first.\n";
        exit(1);
    }

    // 2. Create test file
    echo "2. Creating test file...\n";
    $testFile = storage_path('app/test_upload.txt');
    file_put_contents($testFile, "Test file for Google Drive upload\nCreated at: " . date('Y-m-d H:i:s'));
    echo "   ✓ Test file created: {$testFile}\n\n";

    // 3. Initialize Google Drive Service
    echo "3. Initializing Google Drive Service...\n";
    try {
        $googleDrive = new GoogleDriveService();
        echo "   ✓ Service initialized\n\n";
    } catch (\Exception $e) {
        echo "   ✗ Service initialization failed\n";
        echo "   Error: " . $e->getMessage() . "\n";
        exit(1);
    }

    // 4. Upload test file
    echo "4. Uploading test file to Google Drive...\n";
    echo "   Folder ID: {$spt->google_drive_folder_id}\n";
    echo "   File: test_upload.txt\n";
    echo "   Type: test\n\n";
    
    try {
        $result = $googleDrive->uploadFile(
            $testFile,
            'test_upload.txt',
            $spt->google_drive_folder_id,
            'test'
        );
        
        echo "   ✓ Upload SUCCESSFUL!\n";
        echo "   File ID: {$result['file_id']}\n";
        echo "   File Name: {$result['file_name']}\n";
        echo "   Web Link: {$result['web_link']}\n";
        echo "   MIME Type: {$result['mime_type']}\n";
        echo "   Created: {$result['created_time']}\n\n";
        
        echo "You can now access the file at:\n";
        echo $result['web_link'] . "\n\n";
        
    } catch (\Exception $e) {
        echo "   ✗ Upload FAILED!\n";
        echo "   Error: " . $e->getMessage() . "\n";
        echo "   Trace:\n" . $e->getTraceAsString() . "\n";
        exit(1);
    }

    // 5. Cleanup
    echo "5. Cleaning up test file...\n";
    unlink($testFile);
    echo "   ✓ Test file deleted\n\n";

    echo "=== Test Complete ===\n";
    echo "If you see this message, Google Drive upload is working correctly!\n";

} catch (\Exception $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
