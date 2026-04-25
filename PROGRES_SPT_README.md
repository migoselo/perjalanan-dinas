# Sistem Progres SPT dengan Google Drive Integration

## Quick Start Guide

### Prerequisites
- Laravel 10+
- PHP 8.2+
- Google Drive API credentials
- Composer

### Installation Steps

#### 1. Install Google API Client
```bash
composer require google/apiclient
```

#### 2. Setup Database
```bash
php artisan migrate
```
Ini akan membuat table `spt_progres`.

#### 3. Setup Google Drive

**A. Get Google Drive Credentials**
1. Kunjungi [Google Cloud Console](https://console.cloud.google.com/)
2. Buat project baru
3. Enable Google Drive API
4. Buat Service Account
5. Download JSON credentials

**B. Setup Environment**

Edit `.env` dan tambahkan:

**Option 1: Menggunakan JSON credentials langsung**
```env
GOOGLE_DRIVE_CREDENTIALS_JSON='{"type":"service_account","project_id":"...","private_key":"...","client_email":"...", ...}'
GOOGLE_DRIVE_ROOT_FOLDER_ID='your-google-drive-folder-id'
```

**Option 2: Menggunakan file path**
```env
GOOGLE_DRIVE_CREDENTIALS_PATH=/path/to/credentials.json
GOOGLE_DRIVE_ROOT_FOLDER_ID='your-google-drive-folder-id'
```

**C. Create Root Folder di Google Drive**
1. Buka Google Drive
2. Buat folder baru (nama: `SPT_Progress`)
3. Share folder dengan email service account
4. Copy Folder ID dari URL
5. Set ke `.env` sebagai `GOOGLE_DRIVE_ROOT_FOLDER_ID`

#### 4. Verify Setup
```bash
php artisan google-drive:check-setup
```

### Usage

#### Akses Halaman
```
http://localhost/progres
```

#### Fitur Utama

1. **Tambah SPT Baru**
   - Klik "Tambah Nomor SPT"
   - Pilih Travel/Pegawai
   - Isi Nomor SPT
   - Submit (tanpa reload)

2. **Upload File**
   - Klik tombol "Laporan", "PJ", atau "Pembayaran"
   - Pilih file
   - Upload (automatic create Google Drive folder)

3. **Monitor Progress**
   - Lihat tabel dengan status upload
   - Badge "Lengkap" saat semua 3 file terupload
   - Statistik di atas: Total, Lengkap, Belum Lengkap

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/progres` | Tampilkan halaman progres |
| GET | `/progres/data` | Get semua SPT data (AJAX) |
| GET | `/progres/travels` | Get list Travel untuk dropdown |
| POST | `/progres` | Tambah SPT baru |
| POST | `/progres/{id}/upload` | Upload file |
| DELETE | `/progres/{id}/file` | Hapus file |
| DELETE | `/progres/{id}` | Delete SPT |

### Database Structure

Table `spt_progres`:
```
- id
- travel_id (FK)
- nomor_spt
- nomor_spd
- nama_pegawai

File Upload Fields (untuk setiap tipe: laporan, penanggung_jawab, pembayaran):
- {type}_file_id
- {type}_file_name
- {type}_uploaded_at

Google Drive:
- google_drive_folder_id
- google_drive_folder_name
- is_complete (boolean)
```

### Google Drive Folder Structure

```
Root Folder (SPT_Progress)
├── SPT_001_2026/
│   ├── LAP_report.pdf
│   ├── PJ_manager_document.pdf
│   └── PMB_payment_proof.pdf
├── SPT_002_2026/
│   ├── LAP_report.docx
│   ├── PJ_manager_document.pdf
│   └── PMB_payment_proof.xlsx
└── ...
```

### Configuration

File: `config/services.php`

```php
'google' => [
    'drive' => [
        'credentials_path' => env('GOOGLE_DRIVE_CREDENTIALS_PATH'),
        'credentials_json' => env('GOOGLE_DRIVE_CREDENTIALS_JSON'),
        'root_folder_id' => env('GOOGLE_DRIVE_ROOT_FOLDER_ID'),
    ],
],
```

### Commands

**Check Google Drive Setup**
```bash
php artisan google-drive:check-setup
```

**Cleanup Incomplete SPT**
```bash
# Delete incomplete SPT older than 7 days
php artisan spt-progres:cleanup --days=7

# Force without confirmation
php artisan spt-progres:cleanup --days=7 --force
```

### Testing

Run feature tests:
```bash
php artisan test tests/Feature/ProgresControllerTest.php
```

### File Structure

```
app/
├── Services/
│   ├── GoogleDriveService.php      # Google Drive API wrapper
│   └── GoogleDriveHelper.php       # Helper functions
├── Models/
│   └── SPTProgres.php              # SPT Progress model
├── Http/Controllers/
│   └── ProgresController.php       # Main controller
└── Console/Commands/
    ├── CheckGoogleDriveSetup.php   # Verify setup
    └── CleanupIncompleteUploads.php # Cleanup command

resources/views/progres/
└── index.blade.php                 # Main view

database/migrations/
└── 2026_02_03_000000_create_spt_progres_table.php

config/
└── services.php                    # Google Drive config

routes/
└── web.php                         # Routes configuration

tests/Feature/
└── ProgresControllerTest.php       # Feature tests
```

### Common Issues & Solutions

**Issue: "Google Drive credentials not found"**
- Pastikan .env memiliki GOOGLE_DRIVE_CREDENTIALS_JSON atau GOOGLE_DRIVE_CREDENTIALS_PATH
- Pastikan JSON credentials valid

**Issue: "Failed to create folder"**
- Service account email belum di-share ke root folder
- Pastikan folder ID benar

**Issue: File upload tidak berfungsi**
- Check browser console untuk error
- Verify file size < 50MB
- Check CSRF token ada di form

**Issue: Tabel tidak refresh**
- Check network tab di browser DevTools
- Verify AJAX endpoint accessible

### Troubleshooting Commands

```bash
# Check setup
php artisan google-drive:check-setup

# Clear config cache
php artisan config:clear

# Run migration
php artisan migrate

# Run tests
php artisan test
```

### Performance Notes

- File upload berjalan synchronously
- Folder creation otomatis per SPT
- Database indexes pada: travel_id, nomor_spt, is_complete
- Max file size: 50MB (dapat diubah di validation)

### Security Notes

- Jangan push credentials JSON ke git
- Gunakan .env dengan git-ignore
- Service account hanya perlu access ke SPT Progress folder
- Implement auth/authorization jika needed

### Future Enhancements

- [ ] Bulk upload dari ZIP
- [ ] Export progress ke Excel
- [ ] Email notification
- [ ] Audit log
- [ ] Search & filter
- [ ] Queue jobs untuk upload besar
- [ ] Webhook integration
- [ ] Archive completed SPT

### Support & Documentation

- Full documentation: [PROGRES_SPT_DOCUMENTATION.md](./PROGRES_SPT_DOCUMENTATION.md)
- Google Drive API: https://developers.google.com/drive/api/v3
- Laravel Docs: https://laravel.com/docs
- Service Account Setup: https://developers.google.com/identity/protocols/oauth2/service-account

---

**Last Updated:** February 3, 2026  
**Version:** 1.0.0
