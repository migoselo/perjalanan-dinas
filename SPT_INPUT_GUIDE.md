# Panduan Input SPT di Sistem Upload Laporan SPT

## 📋 Gambaran Umum

Fitur **Tambah SPT** memungkinkan Anda untuk:
- Menginput data SPT (Surat Perjalanan Tugas) baru langsung dari halaman Progres
- Menghubungkan SPT dengan Travel yang sudah ada
- Menyimpan data ke database tabel `spt_progres`
- Membuat folder otomatis di Google Drive untuk setiap SPT

---

## 🚀 Cara Menggunakan

### 1. Buka Halaman Progres
```
URL: http://localhost/progres
```

### 2. Klik Tombol "Tambah SPT"
- Tombol biru terletak di sebelah kanan search bar
- Ikon: **+ Tambah SPT**

### 3. Isi Form Modal
Modal akan muncul dengan 4 field input:

#### Field 1: Pilih Travel (Wajib Diisi ✓)
- **Tipe:** Dropdown Select
- **Isi:** Daftar nama pegawai dan nomor SPD dari travels yang belum memiliki SPT
- **Catatan:** Travel yang sudah memiliki SPT tidak dapat dipilih lagi (disabled)

#### Field 2: Nomor SPT (Wajib Diisi ✓)
- **Tipe:** Text Input
- **Contoh:** `SPT-2026-001`
- **Validasi:** Tidak boleh kosong

#### Field 3: Nomor SPD (Opsional)
- **Tipe:** Text Input
- **Contoh:** `SPD-2026-001`
- **Catatan:** Bisa diisi atau dikosongkan

#### Field 4: Nama Pegawai (Opsional)
- **Tipe:** Text Input
- **Contoh:** `Budi Santoso`
- **Catatan:** Jika kosong, akan mengambil dari travel yang dipilih

### 4. Klik "Simpan SPT"
- Button akan menampilkan loading spinner
- Sistem akan:
  1. Validasi data di backend
  2. Cek duplikasi nomor SPT
  3. Menyimpan ke database
  4. Membuat folder di Google Drive
  5. Menutup modal
  6. Reload halaman untuk menampilkan data baru

---

## 💾 Data yang Disimpan

Setiap SPT baru akan tersimpan dengan informasi:

```php
[
    'travel_id'                 => ID Travel yang dipilih,
    'nomor_spt'                 => Nomor SPT,
    'nomor_spd'                 => Nomor SPD (bisa null),
    'nama_pegawai'              => Nama Pegawai (bisa null),
    'laporan_file_id'           => null (menunggu upload),
    'penanggung_jawab_file_id'  => null (menunggu upload),
    'pembayaran_file_id'        => null (menunggu upload),
    'google_drive_folder_id'    => Auto-generated oleh GoogleDriveService,
    'google_drive_folder_name'  => Auto-generated oleh GoogleDriveService,
    'is_complete'               => false (belum upload semua file),
]
```

---

## 🔄 Flow Proses

```
┌─────────────────────────────┐
│  User Klik "Tambah SPT"      │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│  Modal Form Muncul           │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│  User Isi Form & Submit      │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│  JavaScript Submit via AJAX  │
│  (POST /progres)             │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│  Backend Validasi:           │
│  - travel_id exists?         │
│  - Nomor SPT duplikasi?      │
└──────────────┬──────────────┘
               │
        ├─────┴─────┐
        │           │
     ✓ Valid    ✗ Error
        │           │
        ▼           ▼
   Save DB    Show Error
        │       Message
        ▼
┌─────────────────────────────┐
│  Google Drive Folder Created │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│  Success Alert Shown         │
│  Modal Ditutup               │
│  Halaman Di-reload           │
└─────────────────────────────┘
```

---

## ⚠️ Validasi & Error Handling

### Error Mungkin Terjadi:

| Error | Penyebab | Solusi |
|-------|----------|--------|
| "Travel sudah memiliki SPT" | Travel yang dipilih sudah punya SPT | Pilih travel yang berbeda |
| "SPT sudah ada dalam daftar" | Nomor SPT sudah digunakan di travel ini | Gunakan nomor SPT yang berbeda |
| "Travel tidak ditemukan" | ID travel tidak valid | Refresh halaman dan coba lagi |
| "Google Drive belum dikonfigurasi" | Kredensial Drive tidak tersimpan | Hubungi admin untuk setup |

---

## 🔐 Keamanan

✓ **CSRF Token Protection:** Setiap form submit dilengkapi CSRF token
✓ **Validation Backend:** Semua input divalidasi di backend
✓ **Authorization:** Hanya yang authorized bisa akses endpoint
✓ **Error Logging:** Kesalahan tercatat di `storage/logs/laravel.log`

---

## 📝 Database Query

Untuk lihat SPT yang sudah ditambahkan:

```sql
SELECT * FROM spt_progres 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)
ORDER BY created_at DESC;
```

---

## 🔗 Relasi Database

```
Travel (1)
   └─── (1:1) ──▶ SPTProgres
   
SPTProgres Fields:
├── id (Primary Key)
├── travel_id (Foreign Key)
├── nomor_spt
├── nomor_spd
├── nama_pegawai
├── laporan_file_id
├── penanggung_jawab_file_id
├── pembayaran_file_id
├── google_drive_folder_id
├── is_complete
└── timestamps
```

---

## 🛠️ Technical Details

### Endpoint
```
POST /progres
```

### Request Format
```json
{
    "travel_id": 1,
    "nomor_spt": "SPT-2026-001",
    "nomor_spd": "SPD-2026-001",
    "nama_pegawai": "Budi Santoso"
}
```

### Response Success
```json
{
    "success": true,
    "message": "SPT berhasil ditambahkan",
    "data": {
        "id": 123,
        "travel_id": 1,
        "nomor_spt": "SPT-2026-001",
        ...
    }
}
```

### Response Error
```json
{
    "success": false,
    "message": "SPT sudah ada dalam daftar"
}
```

---

## 📱 Frontend Implementation

**File:** `resources/views/progres/index.blade.php`

### HTML Modal
- ID: `addSPTModal`
- Form ID: `addSPTForm`
- Fields: travel_id, nomor_spt, nomor_spd, nama_pegawai

### JavaScript Handler
```javascript
// Listen to form submit
document.getElementById('addSPTForm').addEventListener('submit', async function(e) {
    // Validasi form
    // Send AJAX POST request
    // Handle response
    // Show alert
    // Reload page
});
```

---

## ✅ Testing Checklist

- [ ] Halaman `/progres` terbuka tanpa error
- [ ] Tombol "Tambah SPT" visible dan clickable
- [ ] Modal form muncul saat tombol diklik
- [ ] Dropdown Travel menampilkan pilihan
- [ ] Form dapat diisi tanpa error JavaScript
- [ ] Submit button menampilkan loading state
- [ ] Success alert muncul setelah submit
- [ ] Halaman reload otomatis
- [ ] SPT baru tampil di tabel
- [ ] Google Drive folder tercreate untuk SPT baru

---

## 🔄 Next Steps

Setelah SPT ditambahkan, Anda dapat:
1. ✅ Upload file Laporan
2. ✅ Upload file Penanggung Jawab
3. ✅ Upload file Pembayaran
4. ✅ Lihat status completion di tabel

---

## 📞 Support

Jika ada error atau pertanyaan:
1. Cek console browser (F12 → Console)
2. Cek server logs: `storage/logs/laravel.log`
3. Pastikan Google Drive sudah dikonfigurasi
4. Pastikan credentials file ada di `storage/app/google.json`
