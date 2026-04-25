# Visual Implementation Guide - SPT Input Form

## 🎨 UI Layout

```
┌─────────────────────────────────────────────────────────────┐
│                     SISTEM UPLOAD LAPORAN SPT               │
│                                                              │
│  Deskripsi sistem dan informasi cara penggunaan             │
│                                                              │
│  ┌────────────────────────────────────────┐ ┌────────────┐ │
│  │  🔍 Cari berdasarkan nama atau SPT      │ │ ➕ Tambah │ │  ← Button
│  └────────────────────────────────────────┘ └────────────┘ │
│                                                              │
├─────────────────────────────────────────────────────────────┤
│  STATISTIK CARDS                                             │
│  ┌──────────────────────────────────────────────────────────┤
│  │ 📄 Total SPT  │ ✅ Sudah Lengkap │ ❌ Belum Lengkap     │
│  │      12       │         8        │        4              │
│  └──────────────────────────────────────────────────────────┤
│                                                              │
├─────────────────────────────────────────────────────────────┤
│  TABEL SPT                                                   │
│  ┌──────────────────────────────────────────────────────────┤
│  │ No │ Nama  │ SPT  │ SPD  │ Laporan │ PJ │ Bayar │ Status │
│  ├─────────────────────────────────────────────────────────┤
│  │ 1  │ Budi  │ -    │ -    │ ⬆️      │ ⬆️  │ ⬆️    │ Belum  │
│  │ 2  │ Andi  │ -    │ -    │ ⬆️      │ ⬆️  │ ⬆️    │ Belum  │
│  │ 3  │ Citra │ OK   │ OK   │ ✅     │ ✅  │ ✅   │ Lengkap│
│  └──────────────────────────────────────────────────────────┘
│                                                              │
│  ℹ️ Cara Penggunaan:                                        │
│  • Klik tombol Upload...                                   │
│  • Format file: PDF, DOC, XLS, JPG, PNG                   │
│  • File otomatis ke Google Drive                           │
│  • Status otomatis jadi Lengkap                            │
│                                                              │
└─────────────────────────────────────────────────────────────┘

            ↓ USER KLIK "TAMBAH SPT"

┌─────────────────────────────────────────────────────────────┐
│  [X]  Tambah SPT Baru                                       │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Pilih Travel *          [▼ Pilih Travel           ]        │
│                          Budi Santoso (SPD-2026-001)        │
│                          Andi Rahman (SPD-2026-002)         │
│                          [Citra Dewi - DISABLED]            │
│                                                              │
│  Nomor SPT *             [SPT-2026-001            ]         │
│                                                              │
│  Nomor SPD               [SPD-2026-001            ]         │
│                                                              │
│  Nama Pegawai            [Opsional                ]         │
│                                                              │
│                          [Batal] [Simpan SPT]               │
│                                                              │
└─────────────────────────────────────────────────────────────┘

            ↓ FORM DISUBMIT

┌─────────────────────────────────────────────────────────────┐
│  [✓] Sukses: SPT berhasil ditambahkan                       │
│  [×]                                                         │
└─────────────────────────────────────────────────────────────┘

            ↓ PAGE RELOAD OTOMATIS

┌─────────────────────────────────────────────────────────────┐
│  TABEL TERUPDATE                                             │
│  ┌──────────────────────────────────────────────────────────┤
│  │ No │ Nama  │ SPT           │ Laporan │ PJ │ Bayar │Status │
│  ├─────────────────────────────────────────────────────────┤
│  │ 1  │ Budi  │ SPT-2026-001  │ ⬆️      │ ⬆️  │ ⬆️    │ Belum  │  ← NEW
│  │ 2  │ Andi  │ -             │ ⬆️      │ ⬆️  │ ⬆️    │ Belum  │
│  │ 3  │ Citra │ OK            │ ✅     │ ✅  │ ✅   │ Lengkap│
│  └──────────────────────────────────────────────────────────┘
```

---

## 🔄 Technical Architecture

```
FRONTEND                          BACKEND                         DATABASE
┌──────────────────┐            ┌──────────────────┐            ┌─────────┐
│  User Interface  │            │  Laravel Server  │            │ MySQL   │
│                  │            │                  │            │         │
│ [Tambah SPT]     │            │                  │            │ travels │
│      Button      ├────────→   │                  │            │  (id)   │
│                  │            │                  │            │         │
│  Modal Form      │◄─────────  │ Validation       │            │spt_progres
│  ├─ Travel*      │  AJAX      │ ├─ travel_id    │◄──────────→│ (id)
│  ├─ Nomor SPT*   │   POST     │ ├─ nomor_spt    │  Query/    │ (travel_id)
│  ├─ Nomor SPD    │  /progres  │ └─ duplikasi    │  Insert    │ (nomor_spt)
│  └─ Nama         │            │                  │            │ (other fields)
│                  │            │ Save DB          │            │
│ [Simpan SPT]     │            │ Create folder    ├───────────→│
│                  │            │ Return JSON      │            │
│  Alert Success   │◄───────────┤                  │            │
│  Close Modal     │            │                  │            │
│  Reload Page     │            │                  │            │
│                  │            │                  │            │
│  Display New SPT │            │                  │            │
│  in Table        │            │                  │            │
│                  │            │                  │            │
└──────────────────┘            └──────────────────┘            └─────────┘
```

---

## 📊 Data Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│ 1. USER FILLS FORM                                          │
│    travel_id = 1                                            │
│    nomor_spt = "SPT-2026-001"                              │
│    nomor_spd = "SPD-2026-001"                              │
│    nama_pegawai = "Budi Santoso"                           │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. JAVASCRIPT HANDLER (handleSubmit)                        │
│    ├─ Prevent default form submit                          │
│    ├─ Collect form values                                  │
│    ├─ Show loading spinner                                 │
│    └─ Send AJAX POST request                               │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. AJAX REQUEST                                             │
│    POST /progres                                            │
│    Headers:                                                 │
│    ├─ Content-Type: application/json                       │
│    └─ X-CSRF-TOKEN: [token from meta]                      │
│    Body:                                                    │
│    {                                                        │
│      travel_id: 1,                                          │
│      nomor_spt: "SPT-2026-001",                            │
│      nomor_spd: "SPD-2026-001",                            │
│      nama_pegawai: "Budi Santoso"                          │
│    }                                                        │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. BACKEND PROCESSING (ProgresController@store)             │
│    ├─ CSRF middleware validates token                      │
│    ├─ Input validation                                     │
│    │  ├─ travel_id exists?                                 │
│    │  └─ nomor_spt not empty?                              │
│    ├─ Check duplikasi                                      │
│    │  └─ WHERE travel_id=1 AND nomor_spt="..."            │
│    ├─ Save to database                                     │
│    │  └─ INSERT INTO spt_progres                           │
│    └─ Create Google Drive folder                           │
│       └─ GoogleDriveService::createSPTFolder()             │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. RESPONSE (JSON)                                          │
│    Status: 200 OK                                           │
│    {                                                        │
│      "success": true,                                       │
│      "message": "SPT berhasil ditambahkan",               │
│      "data": {                                              │
│        "id": 123,                                           │
│        "travel_id": 1,                                      │
│        "nomor_spt": "SPT-2026-001",                        │
│        "google_drive_folder_id": "abc123...",             │
│        ...                                                  │
│      }                                                      │
│    }                                                        │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│ 6. FRONTEND RESPONSE HANDLING                               │
│    ├─ Check response.ok && result.success                  │
│    ├─ Show success alert                                   │
│    ├─ Reset form fields                                    │
│    ├─ Close modal                                          │
│    ├─ Disable button (while reloading)                     │
│    └─ Schedule page reload (1000ms)                        │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│ 7. PAGE RELOAD                                              │
│    ├─ Browser refreshes /progres                           │
│    ├─ Backend fetches fresh data                           │
│    │  └─ SELECT * FROM spt_progres with travels           │
│    └─ Table renders with new SPT                           │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎯 Form Field States

### Travel Dropdown
```
┌─ Pilih Travel (label)
│
├─ [▼ -- Pilih Travel -- ] (placeholder)
│
├─ [▼ Budi Santoso (SPD-001)] (enabled, clickable)
│
├─ [▼ Andi Rahman (SPD-002)] (enabled, clickable)
│
└─ [▼ Citra Dewi (SPD-003)] (DISABLED - sudah ada SPT)
```

### Text Input Fields
```
┌─ Nomor SPT * (label dengan red asterisk)
│  [──────────────────────────] (required, empty)
│  Placeholder: "Contoh: SPT-2026-001"
│
├─ Nomor SPD (label tanpa asterisk)
│  [──────────────────────────] (optional, empty)
│  Placeholder: "Opsional"
│
└─ Nama Pegawai (label tanpa asterisk)
   [──────────────────────────] (optional, empty)
   Placeholder: "Opsional"
```

### Error Display
```
Normal State:
[No error message visible]

Error State (display: block):
┌──────────────────────────────────┐
│ ⚠️  SPT sudah ada dalam daftar   │  (red background)
│                                  │  (red text)
└──────────────────────────────────┘
```

### Button States

**Normal State:**
```
┌────────────────────────────┐
│  [Batal]  [Simpan SPT]     │
│           (blue button)    │
└────────────────────────────┘
```

**Loading State:**
```
┌────────────────────────────┐
│  [Batal]  [⟳ Menyimpan...] │
│           (disabled, spinner)
└────────────────────────────┘
```

**After Submit:**
```
┌────────────────────────────┐
│  [Batal]  [Simpan SPT]     │
│           (re-enabled)     │
└────────────────────────────┘
```

---

## 🔐 Security Flow

```
CLIENT SIDE                         SERVER SIDE                    DATABASE
┌──────────────┐                   ┌──────────────┐               ┌─────┐
│ HTML Page    │                   │ Laravel App  │               │MySQL│
│              │                   │              │               │     │
│ <meta name=  │───┐               │              │               │     │
│  csrf-token> │   │               │              │               │     │
│              │   └──── Read ────┐│              │               │     │
│              │                  ││ Store in     │               │     │
│              │                  │├─ Variable   │               │     │
│              │                  ││              │               │     │
│ Form Submit  │                  │├─ Attach to  │               │     │
│   with CSRF  ├─ POST /progres ──┤│  Request   │               │     │
│   Header     │  [X-CSRF-TOKEN]  │├─ Middleware│               │     │
│              │                  ││  Validates  │               │     │
│              │                  ││ token match │               │     │
│              │                  │├─ ✓ Match   │               │     │
│              │                  ││              │               │     │
│              │                  │├─ Validate   │               │     │
│              │                  │├─ Input data │               │     │
│              │                  ││              │               │     │
│              │                  │├─ Check      │               │     │
│              │                  ││ travel_id   ├───────────────►  ✓ exists
│              │                  ││ exists?     │   Query      │     │
│              │                  ││              │               │     │
│              │                  │├─ Check      │               │     │
│              │                  ││ duplicate   ├───────────────►  ✓ no dup
│              │                  ││ nomor_spt   │   Query      │     │
│              │                  ││              │               │     │
│              │                  │├─ INSERT     │               │     │
│              │◄─ Response (200) ├─ new record  ├───────────────►  ✓ saved
│              │  {success: true} │              │   Insert     │     │
│              │                  │              │               │     │
└──────────────┘                   └──────────────┘               └─────┘
```

---

## 🎬 Step-by-Step Execution

### 1. Page Load
```javascript
document.addEventListener('DOMContentLoaded', function() {
    // Script ini akan dijalankan saat DOM siap
    console.log('✓ Form handler registered');
});
```

### 2. User Clicks Button
```javascript
// Browser detects click event
event.target = <button id="addSPTModal">
// Bootstrap modal.show() dipanggil otomatis
```

### 3. Form Submission
```javascript
// Form listener detects submit event
form.addEventListener('submit', async function(e) {
    e.preventDefault(); // Prevent default form POST
    // Collect values dari form
    // Send AJAX request
});
```

### 4. AJAX Request
```javascript
const response = await fetch('/progres', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': token },
    body: JSON.stringify(data)
});
```

### 5. Response Handling
```javascript
if (response.ok && result.success) {
    // Sukses: close modal, reload page
} else {
    // Error: show error message
}
```

---

## 📈 Component Hierarchy

```
progres/index.blade.php
├── @extends('layouts.app')
│   └── <meta name="csrf-token">  ← Required for CSRF
│
├── Header Section
│   ├── Title & Description
│   ├── Search Bar
│   └── [Tambah SPT Button]  ← Triggers Modal
│
├── Statistics Cards
│   ├── Total SPT
│   ├── Sudah Lengkap
│   └── Belum Lengkap
│
├── Modal Form (id="addSPTModal")  ← Bootstrap modal
│   ├── Header (blue)
│   ├── Form (id="addSPTForm")
│   │   ├── @csrf  ← CSRF token
│   │   ├── Travel Dropdown
│   │   ├── Nomor SPT Input
│   │   ├── Nomor SPD Input
│   │   ├── Nama Pegawai Input
│   │   └── Error Message Div
│   └── Footer (buttons)
│
├── Data Table
│   ├── Header Row
│   └── Data Rows (from $progres)
│
├── Info Footer
│
└── Scripts
    ├── handleFileUpload()
    ├── uploadFileToServer()
    ├── updateUploadButtonState()
    ├── renderTableRow()
    ├── showAlert()
    ├── Form Submit Handler  ← NEW
    └── initializeTableState()
```

---

## ✅ Validation Rules Matrix

```
┌──────────────┬──────────┬─────────────┬──────────────┐
│ Field        │ Required │ Type        │ Constraint   │
├──────────────┼──────────┼─────────────┼──────────────┤
│ travel_id    │ YES ✓    │ Integer     │ exists:       │
│              │          │             │ travels.id    │
├──────────────┼──────────┼─────────────┼──────────────┤
│ nomor_spt    │ YES ✓    │ String      │ unique per   │
│              │          │             │ travel       │
├──────────────┼──────────┼─────────────┼──────────────┤
│ nomor_spd    │ NO       │ String      │ optional     │
│              │          │ (nullable)  │              │
├──────────────┼──────────┼─────────────┼──────────────┤
│ nama_pegawai │ NO       │ String      │ optional     │
│              │          │ (nullable)  │              │
└──────────────┴──────────┴─────────────┴──────────────┘
```

---

## 🎨 Styling Reference

```css
/* Modal Header */
background-color: #2563eb;  /* Blue */
color: white;

/* Input Fields */
border-radius: 6px;
border: 1px solid #d1d5db;  /* Light gray */
padding: Bootstrap default;

/* Error Message */
color: #dc2626;              /* Red */
background-color: #fee2e2;   /* Light red */
border-radius: 6px;

/* Button */
background-color: #2563eb;   /* Blue */
border: none;
padding: 0.5rem 1.5rem;
border-radius: 6px;

/* Label */
color: default;
font-weight: 500;
Required (*) color: red;
```

---

## 📱 Responsive Behavior

```
DESKTOP (1920px+)              TABLET (768px-1023px)        MOBILE (320px-767px)
┌──────────────────┐          ┌────────────────┐            ┌──────────┐
│ Search | Button  │          │ Search|Button  │            │ Search   │
│ [===========][B] │          │ [====][B]      │            │ [======] │
│                  │          │                │            │ [Button] │
│                  │          │                │            │          │
│ TABLE            │          │ TABLE          │            │ TABLE    │
│ [Wide Layout]    │          │ [Scroll X]     │            │ [Stack]  │
│                  │          │                │            │          │
├──────────────────┤          ├────────────────┤            ├──────────┤
│ MODAL                        │ MODAL          │            │ MODAL    │
│ ┌──────────────┐             │ ┌──────────┐   │            │┌────────┐│
│ │┌────────────┐│             │ │┌────────┐│   │            ││┌──────┐││
│ ││ Form      ││             │ ││ Form  ││   │            │││Form ││││
│ ││ Fields    ││             │ ││ Stack ││   │            │││Stack│││
│ │└────────────┘│             │ │└────────┘│   │            ││└──────┘││
│ └──────────────┘             │ └──────────┘   │            │└────────┘│
│                              │                │            │          │
└──────────────────┘           └────────────────┘            └──────────┘
```

---

## 🔄 State Transitions

```
[INITIAL]
  ↓
[WAITING FOR INPUT]
  ├─ Modal open
  ├─ Form empty
  └─ Submit button enabled
  ↓
[INPUT PHASE]
  ├─ User types data
  └─ No validation yet (frontend)
  ↓
[SUBMITTING]
  ├─ Button disabled
  ├─ Spinner showing
  └─ AJAX request sent
  ↓
┌─────────────────┬───────────────┐
│                 │               │
▼                 ▼               ▼
[SUCCESS]    [ERROR - DISPLAY]  [ERROR - NETWORK]
│            │                  │
├─ Alert    ├─ Show message    └─ Show message
├─ Reset    ├─ Keep form          (network error)
├─ Close    └─ Keep modal
└─ Reload
   Page
```

---

## 🎓 Complete Execution Timeline

```
T0:  Page loads
     └─ Script registers form submit listener

T1:  User clicks "Tambah SPT" button
     └─ Modal shows (via Bootstrap)

T2:  User fills form fields
     └─ No validation errors shown yet

T3:  User clicks "Simpan SPT"
     └─ Form submit event triggered

T4:  JavaScript handler runs
     ├─ Collects form values
     ├─ Shows loading state
     └─ Sends AJAX request

T5:  AJAX request in flight
     └─ POST /progres to Laravel

T6:  Backend processes request
     ├─ Validates CSRF token
     ├─ Validates input data
     ├─ Checks for duplicates
     ├─ Saves to database
     ├─ Creates Google Drive folder
     └─ Returns JSON response

T7:  Frontend receives response
     ├─ Checks if successful
     └─ Success: show alert, close modal
     └─ Error: show error message

T8:  1000ms delay
     └─ Browser reloads page

T9:  Page reloads
     ├─ Fresh data from database
     ├─ New SPT appears in table
     └─ Ready for next action

Total Time: ~2-3 seconds (including network + processing)
```

---

## 🎯 Key Integration Points

```
FORM ──────┐
MODAL ─────┼─→ JavaScript Handler ──→ Fetch API ──→ Browser Network
CSRF TOKEN─┘                               ↓
                                        Laravel
                                           ↓
                              ProgresController::store
                              ValidationMiddleware
                              CSRF Middleware
                                           ↓
                                        Database
                                        (INSERT)
                                           ↓
                                      Google Drive
                                      (Create folder)
                                           ↓
                                      JSON Response
                                           ↓
                                    Frontend Handler
                                    (Alert, reload)
```

This visual guide provides a comprehensive understanding of the SPT Input Form implementation!
