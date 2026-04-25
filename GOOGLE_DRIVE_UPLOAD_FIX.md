# Google Drive Upload - FIX Documentation

## 🔧 Perbaikan yang Dilakukan

Masalah utama: **Upload file tidak masuk ke Google Drive (silent fail)**

### 1. Controller Upload FIX (`ProgresController.php`)

**Masalah Sebelumnya:**
- Controller hanya menyimpan ke local storage, **tidak ada upload ke Google Drive sama sekali**
- Tidak ada error handling yang detail
- Tidak ada logging untuk debugging

**Perbaikan:**

#### a. Upload ke Google Drive dengan Parent Folder ID
```php
// SEBELUM (SALAH):
$filePath = $file->store('uploads/spt', 'local');
$fileId = 'local_' . time() . '_' . rand(1000, 9999);

// SESUDAH (BENAR):
$uploadResult = $this->googleDrive->uploadFile(
    $fullPath,
    $fileName,
    $sptProgres->google_drive_folder_id, // ← PARENT FOLDER ID
    $fileType
);
$googleFileId = $uploadResult['file_id'];
```

#### b. Try-Catch dengan Detailed Logging
```php
try {
    $uploadResult = $this->googleDrive->uploadFile(...);
    $googleFileId = $uploadResult['file_id'];
    
    \Log::info('Google Drive upload successful', [
        'file_id' => $googleFileId,
        'web_link' => $uploadResult['web_link'],
    ]);
} catch (\Exception $e) {
    \Log::error('Google Drive upload failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
    // Fallback ke local storage jika Google Drive gagal
    $googleFileId = null;
}
```

#### c. Multipart/Form-Data Validation
```php
if (!$request->hasFile('file')) {
    \Log::error('Request bukan multipart/form-data');
    return response()->json([
        'success' => false,
        'message' => 'Form harus menggunakan multipart/form-data'
    ], 400);
}
```

#### d. Comprehensive Logging di Setiap Tahap
- Input validation logging
- Local storage operation logging  
- Google Drive upload attempt logging
- Database update logging
- Error logging dengan stack trace

**Response Signature Baru:**
```json
{
    "success": true,
    "message": "File berhasil disimpan di Google Drive",
    "file_type": "laporan",
    "file_name": "document.pdf",
    "file_id": "google_drive_file_id_...",
    "storage": "google_drive",  // atau "local"
    "is_complete": false
}
```

### 2. GoogleDriveService FIX (`GoogleDriveService.php`)

**Perbaikan:**

#### a. Namespace Class - Fixed Typo
```php
// SEBELUM (SALAH):
$fileMetadata = new Google_Service_Drive_DriveFile(...);

// SESUDAH (BENAR):
$fileMetadata = new \Google\Service\Drive\DriveFile(...);
```

#### b. Initialize Client Sebelum Upload
```php
public function uploadFile($filePath, $fileName, $folderId, $fileType)
{
    try {
        $this->initializeClient(); // ← PENTING!
        
        // Upload dengan parent folder ID
        $fileMetadata = new \Google\Service\Drive\DriveFile(array(
            'name' => $newFileName,
            'parents' => [$folderId], // ← PARENT FOLDER ID
            'properties' => ['file_type' => $fileType],
        ));
        
        $file = $this->driveService->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => $mimeType,
            'uploadType' => 'multipart',
            'fields' => 'id, name, webViewLink, mimeType, createdTime',
            'supportsAllDrives' => true,
        ));
        
        return [...];
    } catch (Exception $e) {
        \Log::error('Google Drive upload error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        throw new Exception('Failed to upload file: ' . $e->getMessage());
    }
}
```

### 3. Blade View FIX (`resources/views/progres/index.blade.php`)

**Perbaikan:**

#### a. Form Encoding (Sudah Benar)
```html
<form id="formUploadFile" enctype="multipart/form-data">
    <input type="file" class="form-control" id="uploadFileInput" name="file" required>
</form>
```

#### b. JavaScript dengan Enhanced Logging
```javascript
function submitUploadFile() {
    // Debug logging
    console.log('=== UPLOAD STARTED ===');
    console.log('File:', file.name, '| Size:', file.size, 'bytes');
    console.log('FormData check:');
    for (let pair of formData.entries()) {
        console.log('  -', pair[0], ':', pair[1] instanceof File ? `File(${pair[1].name})` : pair[1]);
    }
    
    // Fetch dengan proper headers
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: formData  // FormData otomatis set Content-Type: multipart/form-data
    })
    .then(response => {
        console.log('Response Status:', response.status);
        return response.json().then(data => {
            console.log('Response Data:', data);
            return { status: response.status, data: data };
        });
    })
    .then(({ status, data }) => {
        if (status === 200 && data.success) {
            console.log('✓ Upload SUCCESSFUL!');
            console.log('Storage:', data.storage);
            console.log('File ID:', data.file_id);
            location.reload();
        } else {
            console.error('✗ Upload FAILED!');
            showAlert('danger', 'Gagal', data.message);
        }
    })
    .catch(error => {
        console.error('FETCH ERROR:', error);
        showAlert('danger', 'Error', error.message);
    });
}
```

## 🔍 Cara Debug jika Masih Error

### 1. Cek Laravel Logs
```bash
# Windows PowerShell
Get-Content storage/logs/laravel.log -Tail 50 -Wait

# atau buka file langsung
# c:\laragon\www\perjalanan-dinas\storage\logs\laravel.log
```

### 2. Buka Browser Console (F12)
- Klik **Console** tab
- Lakukan upload
- Lihat log lengkap dari `=== UPLOAD STARTED ===` hingga `✓ Upload SUCCESSFUL!` atau error

### 3. Check Network Request (F12 > Network)
- Lakukan upload
- Lihat request ke `/progres/{id}/upload`
- Cek:
  - **Request Headers**: Ada `X-CSRF-TOKEN` dan `Content-Type: multipart/form-data`
  - **Request Payload**: Ada `file` dan `file_type`
  - **Response**: JSON dengan `success: true` atau `false`

### 4. Check Google Drive API Status
```php
// Di controller, tambahkan:
dd([
    'service_initialized' => (bool)$this->googleDrive,
    'folder_id' => $sptProgres->google_drive_folder_id,
    'credentials_path' => config('services.google.drive.credentials_path'),
]);
```

### 5. Cek Database
```php
// Laravel Tinker
$ php artisan tinker
> $spt = App\Models\SPTProgres::find(1);
> $spt->google_drive_folder_id;      // Should have folder ID
> $spt->laporan_file_id;               // Should have file ID setelah upload
```

## 📋 Checklist Sebelum Upload

- [ ] `.env` memiliki `GOOGLE_DRIVE_CREDENTIALS_PATH` dan `GOOGLE_DRIVE_ROOT_FOLDER_ID`
- [ ] Google credentials JSON file exist dan valid
- [ ] `GOOGLE_DRIVE_ROOT_FOLDER_ID` adalah folder ID yang benar di Google Drive
- [ ] Folder "SPT_..." sudah dibuat di Google Drive (auto-create via `store()` endpoint)
- [ ] Browser developer console menunjukkan upload flow yang complete
- [ ] Tidak ada 403/401 errors di Google Drive API

## ✅ Test Upload

### Local Storage Fallback Test
1. Disable Google Drive service temporarily
2. Upload file
3. File harus tersimpan di `storage/app/uploads/spt/`
4. Database field `laporan_file_id` harus ada value: `local_TIMESTAMP_RANDOM`

### Google Drive Integration Test
1. Enable Google Drive service
2. Ensure folder sudah ada di Google Drive
3. Upload file
4. Check Laravel logs untuk "Google Drive upload successful"
5. File harus appear di Google Drive folder
6. Database field `laporan_file_id` harus ada Google Drive file ID (bukan local_)

## 📁 File yang Diperbaiki

1. ✅ `app/Http/Controllers/ProgresController.php` - Upload logic FIX
2. ✅ `app/Services/GoogleDriveService.php` - Google Drive integration FIX  
3. ✅ `resources/views/progres/index.blade.php` - JavaScript logging enhancement

## 🚀 Next Steps

Jika masih ada error setelah perbaikan:
1. Check `storage/logs/laravel.log` untuk error message detail
2. Lihat browser console F12 untuk client-side flow
3. Test Google Drive API credentials dengan `php artisan tinker`
4. Pastikan folder ID di database sudah ada (dari `store()` endpoint)
