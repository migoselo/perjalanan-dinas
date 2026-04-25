# FAQ - Progres SPT System

## Instalasi & Setup

### Q: Bagaimana cara install sistem ini?
**A:** Ikuti langkah-langkah di `PROGRES_SPT_README.md`:
1. Run `composer require google/apiclient`
2. Run `php artisan migrate`
3. Setup Google Drive credentials
4. Run `php artisan google-drive:check-setup`

### Q: Apa saja file yang ditambahkan?
**A:** 
- Migration: `database/migrations/2026_02_03_000000_create_spt_progres_table.php`
- Model: `app/Models/SPTProgres.php`
- Controller: `app/Http/Controllers/ProgresController.php`
- Services: `app/Services/GoogleDriveService.php`, `GoogleDriveHelper.php`
- Views: `resources/views/progres/index.blade.php`
- Routes: Added ke `routes/web.php`
- Commands: `CheckGoogleDriveSetup.php`, `CleanupIncompleteUploads.php`

### Q: Berapa banyak file yang di-upload?
**A:** 3 jenis file per SPT:
1. Laporan
2. Penanggung Jawab (PJ)
3. Pembayaran

### Q: Apakah bisa upload lebih dari 1 file per tipe?
**A:** Tidak, sistem saat ini hanya menerima 1 file per tipe. Jika perlu upload ulang, delete yang lama terlebih dahulu.

---

## Google Drive Setup

### Q: Bagaimana setup Google Drive API?
**A:** 
1. Buka https://console.cloud.google.com/
2. Buat project atau gunakan yang ada
3. Enable "Google Drive API"
4. Buat Service Account
5. Download JSON credentials
6. Buat folder di Google Drive
7. Share dengan email service account
8. Update `.env` dengan credentials & Folder ID

Detail lengkap: `PROGRES_ENV_EXAMPLE.md`

### Q: Apa bedanya Option 1 (JSON string) vs Option 2 (file path)?
**A:**
- **Option 1 (JSON string)**: Lebih aman untuk production, credentials langsung di .env
- **Option 2 (file path)**: Lebih praktis untuk development, file terpisah dari .env

Rekomendasikan Option 1 untuk production.

### Q: Bagaimana cara mendapatkan Google Drive Folder ID?
**A:**
1. Buka folder di Google Drive
2. Lihat URL: `https://drive.google.com/drive/folders/FOLDER_ID`
3. Copy FOLDER_ID (string panjang setelah `/folders/`)
4. Paste ke `.env` sebagai `GOOGLE_DRIVE_ROOT_FOLDER_ID`

### Q: Berapa quota Google Drive API?
**A:**
- Default: 1 juta requests per hari
- Requests dihitung per operasi: create folder, upload, delete, etc.
- Untuk volume tinggi, request quota increase ke Google

### Q: Bisa upload lebih dari 50MB?
**A:** 
- Default maximum: 50 MB
- Edit validation di `ProgresController.php` di method `uploadFile()`:
  ```php
  'file' => 'required|file|max:52428800', // ubah 52428800 (50MB) ke nilai yang diinginkan
  ```
- Hati-hati dengan performance saat file besar

---

## Penggunaan Sistem

### Q: Bagaimana cara akses halaman progres?
**A:** 
- URL: `http://localhost/progres` (untuk development)
- URL: `https://yourdomain.com/progres` (untuk production)

### Q: Bagaimana cara tambah SPT baru?
**A:**
1. Click tombol "Tambah Nomor SPT"
2. Modal akan muncul
3. Pilih Travel/Pegawai dari dropdown
4. Isi Nomor SPT
5. Click "Tambahkan"
6. Data akan langsung muncul di tabel (no reload)

### Q: Bagaimana cara upload file?
**A:**
1. Click tombol "Laporan", "PJ", atau "Pembayaran" di baris SPT
2. Modal upload akan muncul
3. Pilih file (max 50MB)
4. Click "Upload"
5. File akan upload ke Google Drive & database akan update
6. Jika semua 3 file uploaded, status berubah "Lengkap"

### Q: Bagaimana cara delete file yang sudah upload?
**A:**
1. Click tombol delete (sampah) di sebelah file yang sudah upload
2. Confirm
3. File akan dihapus dari Google Drive & database

### Q: Bagaimana cara delete SPT dari daftar?
**A:**
1. Belum ada button delete per SPT di halaman utama
2. Solusi: Akses via API atau tambahkan delete button di view
3. Via API: `DELETE /progres/{id}`
4. Akan menghapus semua file & folder di Google Drive

---

## Fitur & Fungsionalitas

### Q: Apa itu badge "Lengkap" dan "Belum Lengkap"?
**A:**
- **Lengkap**: Semua 3 file (Laporan, PJ, Pembayaran) sudah di-upload
- **Belum Lengkap**: Minimal 1 dari 3 file masih belum di-upload

### Q: Bagaimana folder structure di Google Drive?
**A:**
```
Root Folder (SPT_Progress)
├── SPT_001_2026/
│   ├── LAP_report.pdf
│   ├── PJ_manager.pdf
│   └── PMB_payment.pdf
├── SPT_002_2026/
│   └── ...
```

Folder dibuat otomatis saat pertama kali upload file untuk SPT tersebut.

### Q: Bagaimana struktur database?
**A:** Lihat table `spt_progres` dengan kolom:
- `nomor_spt`, `nomor_spd`, `nama_pegawai`
- `laporan_file_id`, `penanggung_jawab_file_id`, `pembayaran_file_id`
- `google_drive_folder_id`, `is_complete`
- `created_at`, `updated_at`

### Q: Apakah bisa download file dari halaman ini?
**A:** Tidak di halaman ini, tapi Anda bisa:
1. Click file name di halaman untuk membuka di Google Drive
2. Download dari Google Drive langsung
3. Implementasi download feature di controller jika perlu

---

## Masalah & Troubleshooting

### Q: Error "Google Drive credentials not found"
**A:**
1. Check `.env` file
2. Pastikan `GOOGLE_DRIVE_CREDENTIALS_JSON` atau `GOOGLE_DRIVE_CREDENTIALS_PATH` ada
3. Jika pakai JSON string, pastikan valid JSON format
4. Run: `php artisan google-drive:check-setup` untuk verify

### Q: Error "Failed to create folder"
**A:**
1. Service account email belum di-share ke root folder
2. Steps:
   - Copy email dari JSON credentials (xxx@xxx.iam.gserviceaccount.com)
   - Buka root folder di Google Drive
   - Click Share & tambahkan email tersebut
   - Beri akses "Editor"
3. Run: `php artisan google-drive:check-setup` untuk verify

### Q: Upload file tidak berfungsi
**A:**
1. Check browser console (F12 → Console)
2. Check network tab untuk error response
3. Verify file size < 50MB
4. Verify CSRF token ada di form
5. Check server logs: `storage/logs/laravel.log`

### Q: Tabel tidak refresh setelah upload
**A:**
1. Check browser console untuk AJAX errors
2. Verify URL endpoint correct
3. Check network tab untuk response
4. Clear browser cache (Ctrl+Shift+Delete)
5. Try manual refresh (F5)

### Q: Error "The zip extension and unzip/7z commands are both missing"
**A:**
- Warning ini bisa diabaikan saat development
- Composer akan retry download dari source
- Untuk production, enable zip extension di php.ini

### Q: Bagaimana check error logs?
**A:**
- File: `storage/logs/laravel.log`
- Real-time: `php artisan tail`
- Filter: `tail -f storage/logs/laravel.log | grep ERROR`

---

## Database & Migration

### Q: Bagaimana run migration?
**A:**
```bash
# Run all pending migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Reset all migrations
php artisan migrate:reset

# Refresh (reset + migrate)
php artisan migrate:refresh
```

### Q: Bagaimana add custom fields ke spt_progres?
**A:**
1. Buat migration baru: `php artisan make:migration add_custom_field_to_spt_progres_table`
2. Edit migration file
3. Run: `php artisan migrate`
4. Update model di `app/Models/SPTProgres.php` (fillable, casts)

### Q: Bagaimana backup data?
**A:**
```bash
# Export database
mysqldump -u user -p database_name > backup.sql

# Export SPT Progres table
mysqldump -u user -p database_name spt_progres > spt_progres_backup.sql
```

---

## Testing & Quality Assurance

### Q: Bagaimana run tests?
**A:**
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ProgresControllerTest.php

# Run with coverage
php artisan test --coverage
```

### Q: Bagaimana test Google Drive upload?
**A:**
- Current tests tidak mock Google Drive (integration tests)
- Untuk unit tests, modify test untuk mock GoogleDriveService
- Manual test: Access halaman & upload file sebenarnya

### Q: Bagaimana test tanpa API quota?
**A:**
- Mock GoogleDriveService di tests
- Atau buat separate test environment dengan test project
- Reference: `tests/Feature/ProgresControllerTest.php`

---

## Performance & Optimization

### Q: Bagaimana optimasi upload file besar?
**A:**
- Current: Synchronous upload
- For large files: Implement queue jobs
- Future: Add chunked upload untuk file > 100MB

### Q: Bagaimana handle concurrent uploads?
**A:**
- Current: Synchronous, tidak handle race conditions
- Future: Add file locking mechanism
- Database transactions sudah diterapkan

### Q: Berapa banyak SPT yang bisa handle?
**A:**
- Database: Bisa ribuan (no limit)
- Google Drive: Max 5 juta files per folder (limit Google)
- Performance: Tergantung server spec
- Rekomendasi: Archive old SPT data jika > 10.000 entries

---

## Deployment & Production

### Q: Bagaimana deploy ke production?
**A:**
1. Push code ke repository
2. Pull di production server
3. Setup .env dengan credentials
4. Run `php artisan migrate`
5. Run `php artisan google-drive:check-setup`
6. Setup SSL certificate
7. Configure nginx/apache untuk file upload limits
8. Setup cron job untuk cleanup (optional)

### Q: Bagaimana setup cron job untuk cleanup?
**A:**
```bash
# Edit crontab
crontab -e

# Add this line (cleanup every day at 2 AM)
0 2 * * * cd /path/to/app && php artisan spt-progres:cleanup --days=30 --force
```

### Q: Berapa besar storage yang dibutuhkan?
**A:**
- Database: Minimal (hanya store file IDs & metadata)
- Google Drive: Sesuai total file size upload
- Local storage: Temporary (auto cleanup setelah upload)
- Rekomendasi: Monitor Google Drive storage quota

### Q: Bagaimana handling jika Google Drive down?
**A:**
- Upload akan gagal
- User akan melihat error message
- Data tetap tersimpan di database (tanpa file ID)
- Bisa retry upload saat Google Drive kembali

---

## Maintenance & Monitoring

### Q: Bagaimana monitoring sistem?
**A:**
1. Check application logs: `storage/logs/laravel.log`
2. Monitor database: Check table sizes & indices
3. Monitor Google Drive: Check folder & files count
4. Monitor server: CPU, memory, disk space

### Q: Bagaimana cleanup old data?
**A:**
```bash
# Delete incomplete SPT older than 30 days
php artisan spt-progres:cleanup --days=30

# Force without confirmation
php artisan spt-progres:cleanup --days=30 --force
```

### Q: Bagaimana recovery jika data corrupt?
**A:**
1. Database restore dari backup
2. Google Drive files masih tersimpan dengan file ID
3. Update database dengan correct file IDs
4. Manual cleanup jika perlu

---

## Support & Further Help

### Q: Bantuan lebih lanjut dimana?
**A:**
1. Dokumentasi: `PROGRES_SPT_DOCUMENTATION.md` (comprehensive)
2. Quick Start: `PROGRES_SPT_README.md` (quick reference)
3. Setup Guide: `PROGRES_ENV_EXAMPLE.md` (configuration)
4. Checklist: `PROGRES_SPT_CHECKLIST.md` (implementation status)
5. Code: Check comments di `app/Services/GoogleDriveService.php`
6. Laravel Docs: https://laravel.com/docs

### Q: Bagaimana report bugs?
**A:**
1. Check logs: `storage/logs/laravel.log`
2. Check error message di UI
3. Reproduce issue
4. Report dengan:
   - Error message lengkap
   - Steps untuk reproduce
   - Server info (PHP version, etc.)
   - Browser info

---

**Last Updated**: February 3, 2026
**Version**: 1.0.0
