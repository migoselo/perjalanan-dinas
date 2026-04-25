# Quick Installation Guide - Copy & Paste Commands

## Step 1: Install Google API Client

```bash
cd c:\laragon\www\perjalanan-dinas
composer require google/apiclient
```

## Step 2: Run Database Migration

```bash
php artisan migrate
```

This will create the `spt_progres` table.

## Step 3: Get Google Drive Credentials

1. Visit: https://console.cloud.google.com/
2. Create new project or select existing
3. Search for "Google Drive API" → Enable
4. Go to: APIs & Services → Credentials
5. Click: Create Credentials → Service Account
6. Fill name: "SPT Progress System" → Create
7. Skip step 2 → In step 3, Click "Create Key" → JSON
8. JSON file will auto-download

## Step 4: Create Google Drive Root Folder

1. Open: https://drive.google.com
2. Create new folder: `SPT_Progress`
3. Copy folder ID from URL (after `/folders/`)
4. Share folder with service account email (from JSON)

## Step 5: Update .env File

Add these lines to your `.env` file:

### Option A: Using JSON String (Recommended for Production)

```env
# Copy entire JSON content into this variable
GOOGLE_DRIVE_CREDENTIALS_JSON={"type":"service_account","project_id":"...","private_key":"...","client_email":"...","client_id":"...","auth_uri":"https://accounts.google.com/o/oauth2/auth","token_uri":"https://oauth2.googleapis.com/token","auth_provider_x509_cert_url":"https://www.googleapis.com/oauth2/v1/certs","client_x509_cert_url":"..."}

# Google Drive root folder ID
GOOGLE_DRIVE_ROOT_FOLDER_ID=1aBcDeFgHiJkLmNoPqRsT
```

### Option B: Using File Path (Recommended for Development)

```env
# Path to downloaded JSON credentials file
GOOGLE_DRIVE_CREDENTIALS_PATH=/path/to/credentials.json

# Google Drive root folder ID
GOOGLE_DRIVE_ROOT_FOLDER_ID=1aBcDeFgHiJkLmNoPqRsT
```

## Step 6: Verify Setup

```bash
php artisan google-drive:check-setup
```

Expected output:
```
✓ Credentials configuration
✓ GOOGLE_DRIVE_ROOT_FOLDER_ID: 1aBcDeF...
✓ Successfully connected to Google Drive API
✓ All checks passed!
```

## Step 7: Access System

Visit: http://localhost/progres

---

## ✅ Quick Test

### 1. Add New SPT

Click "Tambah Nomor SPT" and fill:
- Travel: Select from dropdown
- Nomor SPT: `SPT-001/2026`
- Nomor SPD: `SPD-001/2026`

Click "Tambahkan"

### 2. Upload File

1. Click "Laporan" button
2. Select any PDF file
3. Click "Upload"
4. Check:
   - File uploaded to Google Drive (check folder)
   - Status changed to green checkmark in table
   - Badge shows "Lengkap" if all 3 uploaded

---

## 🔧 Troubleshooting

### Error: "Google Drive credentials not found"

```bash
# Check if .env has the credentials
grep -i "GOOGLE_DRIVE_CREDENTIALS" .env

# If missing, edit .env and add the credentials
```

### Error: "Failed to create folder"

1. Check service account email in JSON: look for `"client_email": "xxx@xxx.iam.gserviceaccount.com"`
2. Open Google Drive folder
3. Click Share
4. Add that email and give "Editor" access

### Error: "File upload fails silently"

1. Check browser console: Press F12 → Console tab
2. Look for error messages
3. Check storage logs: `storage/logs/laravel.log`

### Run check command again

```bash
php artisan google-drive:check-setup
```

---

## 📁 File Structure Created

```
app/
├── Models/SPTProgres.php
├── Http/Controllers/ProgresController.php
├── Services/
│   ├── GoogleDriveService.php
│   └── GoogleDriveHelper.php
└── Console/Commands/
    ├── CheckGoogleDriveSetup.php
    └── CleanupIncompleteUploads.php

database/
└── migrations/2026_02_03_000000_create_spt_progres_table.php

resources/views/progres/
└── index.blade.php

tests/Feature/
└── ProgresControllerTest.php

config/
└── services.php (updated)

routes/
└── web.php (updated)
```

---

## 📊 Database Table Created

```
spt_progres table with columns:
- id, travel_id (FK)
- nomor_spt, nomor_spd, nama_pegawai
- laporan_file_id, laporan_file_name, laporan_uploaded_at
- penanggung_jawab_file_id, penanggung_jawab_file_name, penanggung_jawab_uploaded_at
- pembayaran_file_id, pembayaran_file_name, pembayaran_uploaded_at
- google_drive_folder_id, google_drive_folder_name
- is_complete (boolean)
- created_at, updated_at
```

---

## 🚀 Next Commands

```bash
# Run tests
php artisan test

# Clear caches if needed
php artisan config:clear
php artisan cache:clear

# Check logs
tail -f storage/logs/laravel.log

# Cleanup old entries (optional)
php artisan spt-progres:cleanup --days=30
```

---

## 📖 Documentation Files

Read these for more information:
- `PROGRES_SPT_README.md` - Quick start guide
- `PROGRES_SPT_DOCUMENTATION.md` - Comprehensive documentation
- `PROGRES_SPT_FAQ.md` - Frequently asked questions
- `PROGRES_SPT_CHECKLIST.md` - Implementation checklist
- `RINGKASAN_IMPLEMENTASI.md` - Implementation summary

---

## ⏱️ Expected Time

- Installation: 5 minutes
- Google Drive setup: 10 minutes
- Verification: 5 minutes
- Total: ~20 minutes

---

**Status**: Ready to use
**Last Updated**: February 3, 2026
