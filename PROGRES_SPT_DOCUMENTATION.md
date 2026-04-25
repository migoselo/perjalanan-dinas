# Dokumentasi Sistem Progres SPT

## Overview
Sistem ini memungkinkan tracking progress upload dokumen SPT ke Google Drive secara otomatis.

### Fitur Utama:
1. ✅ Tabel yang menampilkan Nama, Nomor SPT, Nomor SPD
2. ✅ Upload 3 jenis file per SPT: Laporan, Penanggung Jawab, Pembayaran
3. ✅ Upload per baris SPT
4. ✅ Tombol Upload jika file belum di-upload, Ikon centang jika sudah
5. ✅ Status disimpan di database
6. ✅ SPT dianggap "Lengkap" jika semua 3 file sudah di-upload
7. ✅ File langsung disimpan ke Google Drive
8. ✅ Setiap SPT memiliki folder sendiri di Google Drive
9. ✅ FileId Google Drive disimpan ke database
10. ✅ Ringkasan: Total, Sudah Lengkap, Belum Lengkap
11. ✅ Modal form untuk tambah SPT (AJAX, tanpa reload)
12. ✅ Upload file tanpa reload (AJAX)

---

## Instalasi & Setup

### 1. Database Migration
Jalankan migration untuk membuat tabel `spt_progres`:
```bash
php artisan migrate
```

Tabel akan membuat kolom:
- `nomor_spt`, `nomor_spd`, `nama_pegawai`
- `laporan_file_id`, `laporan_file_name`, `laporan_uploaded_at`
- `penanggung_jawab_file_id`, `penanggung_jawab_file_name`, `penanggung_jawab_uploaded_at`
- `pembayaran_file_id`, `pembayaran_file_name`, `pembayaran_uploaded_at`
- `google_drive_folder_id`, `google_drive_folder_name`
- `is_complete` (boolean)

### 2. Google Drive API Setup

#### A. Aktifkan Google Drive API
1. Kunjungi https://console.cloud.google.com/
2. Buat project baru atau gunakan yang sudah ada
3. Cari "Google Drive API" di Marketplace
4. Klik "Enable"

#### B. Buat Service Account
1. Buka menu **APIs & Services** → **Credentials**
2. Klik **Create Credentials** → **Service Account**
3. Isi nama service account (contoh: `spt-progres-system`)
4. Klik **Create and Continue**
5. Skip step 2 (opsional)
6. Di step 3, klik **Create Key** → **JSON**
7. JSON key akan otomatis download

#### C. Setup Environment Variables
1. Edit file `.env` dan tambahkan:
```
GOOGLE_DRIVE_ROOT_FOLDER_ID=<FOLDER_ID>
GOOGLE_DRIVE_CREDENTIALS_JSON=<ISI_FILE_JSON>
```

2. Atau gunakan path ke file:
```
GOOGLE_DRIVE_CREDENTIALS_PATH=/path/to/credentials.json
GOOGLE_DRIVE_ROOT_FOLDER_ID=<FOLDER_ID>
```

#### D. Setup Root Folder di Google Drive
1. Buka Google Drive
2. Buat folder baru (nama: `SPT_Progress` atau sesuai keinginan)
3. Copy folder ID dari URL:
   - URL format: `https://drive.google.com/drive/folders/FOLDER_ID`
   - FOLDER_ID adalah setelah `/folders/`
4. Paste ke `.env` sebagai `GOOGLE_DRIVE_ROOT_FOLDER_ID`

#### E. Share Folder dengan Service Account
1. Copy email service account dari JSON credentials (format: `xxx@xxx.iam.gserviceaccount.com`)
2. Kembali ke folder di Google Drive
3. Klik **Share** dan tambahkan email service account
4. Beri akses "Editor"

### 3. Struktur Routes
Routes sudah dikonfigurasi di `routes/web.php`:
```php
Route::prefix('progres')->group(function () {
    Route::get('/', [ProgresController::class, 'index'])->name('progres.index');
    Route::post('/', [ProgresController::class, 'store'])->name('progres.store');
    Route::get('/data', [ProgresController::class, 'getData'])->name('progres.getData');
    Route::get('/travels', [ProgresController::class, 'getTravelList'])->name('progres.getTravelList');
    Route::post('/{sptProgres}/upload', [ProgresController::class, 'uploadFile'])->name('progres.uploadFile');
    Route::delete('/{sptProgres}/file', [ProgresController::class, 'deleteFile'])->name('progres.deleteFile');
    Route::delete('/{sptProgres}', [ProgresController::class, 'destroy'])->name('progres.destroy');
});
```

Akses halaman di: `http://localhost/progres`

---

## Struktur File

### Database
- Migration: `database/migrations/2026_02_03_000000_create_spt_progres_table.php`

### Models
- Model: `app/Models/SPTProgres.php`

### Services
- Google Drive Service: `app/Services/GoogleDriveService.php`

### Controllers
- Controller: `app/Http/Controllers/ProgresController.php`

### Views
- Main view: `resources/views/progres/index.blade.php`

### Config
- Services config: `config/services.php` (sudah updated dengan Google Drive config)

---

## API Endpoints

### 1. GET /progres
Menampilkan halaman progres dengan tabel SPT

### 2. POST /progres
Tambah SPT baru via AJAX
**Parameters:**
- `travel_id` (required)
- `nomor_spt` (required)
- `nomor_spd` (optional)
- `nama_pegawai` (optional)

**Response:**
```json
{
  "success": true,
  "message": "SPT berhasil ditambahkan",
  "data": {...}
}
```

### 3. GET /progres/data
Get data SPT untuk refresh tabel

**Response:**
```json
{
  "success": true,
  "data": [...],
  "stats": {
    "total": 10,
    "complete": 5,
    "incomplete": 5
  }
}
```

### 4. GET /progres/travels
Get list Travel untuk dropdown

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nama_pegawai": "John Doe",
      "nomor_surat_tugas": "001/2026",
      "nomor_spd": "SPD-001"
    }
  ]
}
```

### 5. POST /progres/{sptProgres}/upload
Upload file untuk SPT tertentu

**Parameters:**
- `file_type` (required): `laporan`, `penanggung_jawab`, `pembayaran`
- `file` (required): file object

**Response:**
```json
{
  "success": true,
  "message": "File berhasil di-upload",
  "data": {
    "file_id": "google_drive_file_id",
    "file_name": "filename",
    "file_type": "laporan",
    "is_complete": false
  }
}
```

### 6. DELETE /progres/{sptProgres}/file
Hapus file dari SPT

**Parameters:**
- `file_type` (required)

**Response:**
```json
{
  "success": true,
  "message": "File berhasil dihapus",
  "data": {
    "file_type": "laporan",
    "is_complete": false
  }
}
```

### 7. DELETE /progres/{sptProgres}
Hapus SPT dari daftar

**Response:**
```json
{
  "success": true,
  "message": "SPT berhasil dihapus"
}
```

---

## Fitur & Fungsi

### Frontend Features
- **Tabel Responsif**: Menampilkan Nama, Nomor SPT, Nomor SPD, Status Upload, Status Lengkap
- **Statistik Cards**: Total SPT, Sudah Lengkap, Belum Lengkap (real-time)
- **Modal Form**: Tambah SPT baru tanpa reload halaman
- **Upload Buttons**: Per file type dengan visual feedback
- **Status Badge**: Menunjukkan "Lengkap" atau "Belum Lengkap"
- **Delete Option**: Hapus file individual atau seluruh SPT
- **Loading Spinner**: Feedback saat upload berlangsung
- **Alert Messages**: Success/Error notifications

### Backend Features
- **Automatic Folder Creation**: Buat folder otomatis di Google Drive per SPT
- **File Management**: Upload, delete, track file via Google Drive API
- **Database Tracking**: Simpan FileId, timestamp, completion status
- **Completion Auto-Check**: Automatically update `is_complete` status
- **Error Handling**: Try-catch dengan logging untuk setiap operasi Google Drive
- **Relationship Management**: Model relationship dengan Travel

---

## Troubleshooting

### 1. Error: "Google Drive credentials not found"
- Pastikan `.env` memiliki `GOOGLE_DRIVE_CREDENTIALS_JSON` atau `GOOGLE_DRIVE_CREDENTIALS_PATH`
- Pastikan JSON credentials valid dari Google Cloud Console

### 2. Error: "Failed to create folder"
- Pastikan service account email sudah di-share ke root folder
- Pastikan folder ID benar

### 3. Error: "File not found"
- Pastikan file yang di-upload ada di temporary storage
- Check storage permissions

### 4. Upload tidak berfungsi
- Check browser console untuk error messages
- Verify CSRF token ada di form
- Check file size (max 50MB)

### 5. Tabel tidak refresh
- Check browser console
- Verify AJAX endpoint URL benar
- Check network tab di DevTools

---

## Testing

### Manual Test
1. Akses `http://localhost/progres`
2. Klik "Tambah Nomor SPT"
3. Pilih Travel, isi Nomor SPT
4. Submit dan cek apakah data muncul di tabel
5. Klik tombol Upload untuk upload file
6. Verify file muncul di Google Drive
7. Cek database `spt_progres` untuk verify data

---

## Catatan Penting

1. **Quota & Limits**:
   - Google Drive API: Default quota 1 juta requests per hari
   - File size: Max 50MB per file (bisa diubah di validation)

2. **Security**:
   - Jangan push credentials JSON ke git
   - Gunakan `.env` dengan git-ignore
   - Service account email hanya perlu access ke folder SPT Progress

3. **Performance**:
   - Folder creation & file upload berjalan synchronously
   - Untuk volume tinggi, pertimbangkan queue jobs
   - Current setup cocok untuk <1000 SPT

4. **Maintenance**:
   - Regular check Google Drive folder structure
   - Archive old SPT data jika sudah complete
   - Monitor Google Drive storage quota

---

## Fitur Tambahan (Future)

1. Bulk upload dari ZIP
2. Download all files untuk SPT tertentu
3. Export progress ke Excel
4. Email notification saat semua file upload
5. Integration dengan approval workflow
6. Audit log untuk setiap upload/delete
7. Search & filter SPT
8. API webhook untuk external integration

---

## Support

Untuk questions atau issues, check:
- Google Drive API docs: https://developers.google.com/drive/api/v3/about-sdk
- Laravel docs: https://laravel.com/docs
- Browser console untuk error details
