# SSL Certificate Error - Solusi Lengkap

## Masalah
```
Error: Upload gagal: Gagal membuat folder di Google Drive: Failed to check folder: 
cURL error 77: error setting certificate file: D:\Projects\Laragon-installer\7.0-W64\etc\ssl\cacert.pem
```

## Penyebab
- Path `cacert.pem` yang Laragon referensikan tidak ada atau tidak accessible
- cURL tidak bisa meload SSL certificate file untuk HTTPS koneksi ke Google API

## Solusi yang Sudah Diterapkan

### 1. **SSLHelper Class** (Automatic)
Saya sudah membuat `App/Helpers/SSLHelper.php` yang:
- ✅ Otomatis mencari `cacert.pem` di berbagai lokasi Laragon yang umum
- ✅ Download certificate bundle dari Mozilla jika tidak ditemukan
- ✅ Fallback ke Guzzle's bundled certificate
- ✅ Normalize paths ke forward slash (kompatibel Windows/Linux)

### 2. **GoogleDriveService Update**
GoogleDriveService.php sekarang menggunakan SSLHelper untuk:
- ✅ Konfigurasi Guzzle client dengan SSL yang tepat
- ✅ Automatic certificate path detection
- ✅ Better error handling

## Jika Masih Terjadi Error

### Option 1: Fix Laragon's cacert.pem (REKOMENDASI)
1. Buka Command Prompt sebagai Administrator
2. Verify Laragon directory:
   ```cmd
   dir C:\laragon\etc\ssl\cacert.pem
   ```
   
3. Jika tidak ada, download dari Mozilla:
   ```cmd
   cd C:\laragon\etc\ssl\
   curl -o cacert.pem https://curl.se/ca/cacert.pem
   ```

4. Atau gunakan Git for Windows certificate (jika terinstall):
   ```cmd
   copy "C:\Program Files\Git\usr\ssl\certs\ca-bundle.crt" "C:\laragon\etc\ssl\cacert.pem"
   ```

### Option 2: Update php.ini
Jika Laragon tidak memiliki cacert.pem yang valid, update `php.ini`:

```ini
; Di C:\laragon\etc\php\x.x\php.ini cari atau tambahkan:

[curl]
curl.cainfo = "C:\laragon\etc\ssl\cacert.pem"

[openssl]
openssl.cafile = "C:\laragon\etc\ssl\cacert.pem"
```

Kemudian restart Laragon (stop MySQL & Apache).

### Option 3: Gunakan SSL Path Environment Variable
```cmd
set CURL_CA_BUNDLE=C:\laragon\etc\ssl\cacert.pem
```

### Option 4: Manual Certificate Download (Final Resort)
System akan otomatis download cacert.pem terbaru ke `storage/app/cacert.pem`:
```
✅ Proses otomatis - tidak perlu action manual
```

## Debugging
Untuk melihat path cacert yang digunakan, cek log:
```
storage/logs/laravel.log
```

Cari entry dengan "Found valid cacert.pem" atau "Downloading Mozilla CA Certificate Bundle"

## Verifikasi
Untuk test koneksi Google Drive:
```bash
php artisan tinker
> use App\Helpers\SSLHelper;
> SSLHelper::getCACertPath()
```

Output akan menunjukkan path yang sedang digunakan.

---
**Created:** Feb 6, 2026  
**Related Error:** cURL error 77  
**Status:** ✅ Fixed - Automatic SSL Helper Implemented
