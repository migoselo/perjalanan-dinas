<?php
// Test Google Drive Upload
// Run: php artisan tinker < test_google_upload.php
// Atau: php -r "require 'artisan'; tinker();"

use App\Services\GoogleDriveService;
use App\Models\SPTProgres;

try {
    echo "🔍 Testing Google Drive Upload Flow...\n\n";
    
    // 1. Test GoogleDriveService initialization
    echo "1️⃣ Initializing GoogleDriveService...\n";
    $drive = new GoogleDriveService();
    echo "✅ GoogleDriveService initialized successfully!\n\n";
    
    // 2. Test folder creation
    echo "2️⃣ Testing folder creation...\n";
    $nomorSPT = "SPT_TEST_" . date('YmdHis');
    $folderResult = $drive->createSPTFolder($nomorSPT);
    echo "✅ Folder created: {$folderResult['folder_name']}\n";
    echo "   Folder ID: {$folderResult['folder_id']}\n";
    echo "   Link: {$folderResult['web_link']}\n\n";
    
    // 3. Test file upload
    echo "3️⃣ Creating test file...\n";
    $testFilePath = storage_path('app/test_upload.txt');
    file_put_contents($testFilePath, "Test file content - " . date('Y-m-d H:i:s'));
    echo "✅ Test file created: {$testFilePath}\n\n";
    
    echo "4️⃣ Uploading test file to Google Drive...\n";
    $uploadResult = $drive->uploadFile(
        $testFilePath,
        'test_upload.txt',
        $folderResult['folder_id'],
        'laporan'
    );
    echo "✅ File uploaded successfully!\n";
    echo "   File ID: {$uploadResult['file_id']}\n";
    echo "   File Name: {$uploadResult['file_name']}\n";
    echo "   Link: {$uploadResult['web_link']}\n\n";
    
    // 4. Clean up
    echo "5️⃣ Cleanup...\n";
    if (file_exists($testFilePath)) {
        unlink($testFilePath);
        echo "✅ Test file deleted\n";
    }
    
    echo "\n✅ SEMUA TEST BERHASIL! Google Drive upload siap digunakan.\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
