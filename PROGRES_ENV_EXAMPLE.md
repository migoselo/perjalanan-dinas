# ==============================================================
# Progres SPT System - Google Drive Configuration Example
# ==============================================================
# Copy ke .env dan update dengan credentials Anda
# ==============================================================

# Option 1: Menggunakan JSON credentials string (recommended untuk production)
# GOOGLE_DRIVE_CREDENTIALS_JSON='{"type":"service_account","project_id":"your-project","private_key":"-----BEGIN PRIVATE KEY-----\nMIIEvQIBA...","client_email":"spt-progres@your-project.iam.gserviceaccount.com",...}'

# Option 2: Menggunakan path ke file JSON (recommended untuk development)
GOOGLE_DRIVE_CREDENTIALS_PATH=/path/to/service-account-credentials.json

# Root Folder ID di Google Drive (tempat semua folder SPT akan disimpan)
# Cara mendapatkan: Buka folder di Google Drive, copy ID dari URL
# URL format: https://drive.google.com/drive/folders/FOLDER_ID
GOOGLE_DRIVE_ROOT_FOLDER_ID=1aBcDeFgHiJkLmNoPqRsT

# ==============================================================
# Cara Setup Google Drive Integration
# ==============================================================
# 1. Kunjungi https://console.cloud.google.com/
# 2. Create project baru atau pilih yang sudah ada
# 3. Enable Google Drive API:
#    - Search "Google Drive API" di Marketplace
#    - Click "Enable"
# 
# 4. Create Service Account:
#    - Go to: APIs & Services → Credentials
#    - Click "Create Credentials" → "Service Account"
#    - Fill name: "SPT Progres System"
#    - Click "Create and Continue"
#    - Skip optional step 2
#    - In step 3, click "Create Key" → "JSON"
#    - JSON file akan auto download
#
# 5. Get Root Folder ID:
#    - Buka Google Drive
#    - Buat folder baru (nama: SPT_Progress)
#    - Copy ID dari URL (setelah /folders/)
#
# 6. Share folder dengan Service Account:
#    - Copy email dari downloaded JSON (xxx@xxx.iam.gserviceaccount.com)
#    - Kembali ke folder di Drive
#    - Click "Share"
#    - Paste email dan beri akses "Editor"
#
# 7. Update .env ini dengan:
#    - Copy isi JSON file ke GOOGLE_DRIVE_CREDENTIALS_JSON
#    - Atau set path ke file JSON di GOOGLE_DRIVE_CREDENTIALS_PATH
#    - Paste Folder ID ke GOOGLE_DRIVE_ROOT_FOLDER_ID
#
# 8. Verify setup:
#    php artisan google-drive:check-setup
#
# ==============================================================

# File Types & Max Size Configuration
# PROGRES_MAX_FILE_SIZE=52428800  # 50MB in bytes
# PROGRES_ALLOWED_FILE_TYPES=pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,png,txt,zip

# Feature Flags
# PROGRES_AUTO_CREATE_FOLDER=true
# PROGRES_AUTO_DELETE_TEMP_FILES=true
# PROGRES_ENABLE_NOTIFICATIONS=false

# Logging
# PROGRES_LOG_UPLOADS=true
# PROGRES_LOG_DELETES=true
# PROGRES_LOG_ERRORS=true
