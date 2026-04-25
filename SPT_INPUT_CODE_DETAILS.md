# Code Implementation Details - SPT Input Form

## 📝 Files Modified

### File: `resources/views/progres/index.blade.php`

---

## 1️⃣ Header Button Section (Lines 15-28)

### BEFORE:
```html
<!-- Search Bar -->
<div style="position: relative;">
    <i class="bi bi-search" style="position: absolute; left: 12px; top: 10px; color: #9ca3af; font-size: 1.25rem;"></i>
    <input type="text" class="form-control" id="searchSPT" 
           placeholder="Cari berdasarkan nama atau nomor SPT..." 
           style="padding-left: 2.5rem; border-radius: 8px; border: 1px solid #d1d5db; height: 2.75rem;">
</div>
</div>
```

### AFTER:
```html
<!-- Search Bar & Add SPT Button -->
<div style="display: flex; gap: 1rem; align-items: center;">
    <div style="position: relative; flex: 1;">
        <i class="bi bi-search" style="position: absolute; left: 12px; top: 10px; color: #9ca3af; font-size: 1.25rem;"></i>
        <input type="text" class="form-control" id="searchSPT" 
               placeholder="Cari berdasarkan nama atau nomor SPT..." 
               style="padding-left: 2.5rem; border-radius: 8px; border: 1px solid #d1d5db; height: 2.75rem;">
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSPTModal" 
            style="background-color: #2563eb; border: none; padding: 0.5rem 1.5rem; border-radius: 6px; white-space: nowrap;">
        <i class="bi bi-plus-circle"></i> Tambah SPT
    </button>
</div>
</div>
```

**Changes:**
- Wrapped search bar and button in flex container
- Added blue "Tambah SPT" button with icon
- Button triggers Bootstrap modal via `data-bs-toggle` and `data-bs-target`

---

## 2️⃣ Modal Form (Lines 29-77)

### NEW CODE:
```html
<!-- Modal Tambah SPT -->
<div class="modal fade" id="addSPTModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #2563eb; color: white;">
                <h5 class="modal-title">Tambah SPT Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSPTForm">
                @csrf
                <div class="modal-body">
                    <!-- Travel Selection -->
                    <div class="mb-3">
                        <label for="travel_id" class="form-label">
                            Pilih Travel <span style="color: red;">*</span>
                        </label>
                        <select class="form-control" id="travel_id" name="travel_id" required style="border-radius: 6px;">
                            <option value="">-- Pilih Travel --</option>
                            @foreach($progres as $item)
                                @if($item->travel)
                                    <option value="{{ $item->travel->id }}" 
                                            @if($item->travel->id) {{ $item->id ? 'disabled' : '' }} @endif>
                                        {{ $item->travel->nama_pegawai }} ({{ $item->travel->nomor_spd ?? '-' }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <small style="color: #6b7280;">Travel yang sudah memiliki SPT tidak bisa dipilih lagi</small>
                    </div>

                    <!-- Nomor SPT -->
                    <div class="mb-3">
                        <label for="nomor_spt" class="form-label">Nomor SPT <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="nomor_spt" name="nomor_spt" 
                               placeholder="Contoh: SPT-2026-001" required style="border-radius: 6px; border: 1px solid #d1d5db;">
                    </div>

                    <!-- Nomor SPD -->
                    <div class="mb-3">
                        <label for="nomor_spd" class="form-label">Nomor SPD</label>
                        <input type="text" class="form-control" id="nomor_spd" name="nomor_spd" 
                               placeholder="Opsional" style="border-radius: 6px; border: 1px solid #d1d5db;">
                    </div>

                    <!-- Nama Pegawai -->
                    <div class="mb-3">
                        <label for="nama_pegawai" class="form-label">Nama Pegawai</label>
                        <input type="text" class="form-control" id="nama_pegawai" name="nama_pegawai" 
                               placeholder="Opsional" style="border-radius: 6px; border: 1px solid #d1d5db;">
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" style="display: none; color: #dc2626; font-size: 0.875rem; margin-bottom: 1rem; padding: 0.75rem; background-color: #fee2e2; border-radius: 6px;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #2563eb; border: none;">Simpan SPT</button>
                </div>
            </form>
        </div>
    </div>
</div>
```

**Features:**
- `@csrf` for CSRF token
- Dynamic travel dropdown with smart disable logic
- 4 form fields with proper labels and placeholders
- Error message container (initially hidden)
- Save and Cancel buttons

---

## 3️⃣ JavaScript Handler (Lines 445-490)

### NEW CODE:
```javascript
// Handle Add SPT Form Submit
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addSPTForm');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const travelId = document.getElementById('travel_id').value;
            const nomorSpt = document.getElementById('nomor_spt').value;
            const nomorSpd = document.getElementById('nomor_spd').value;
            const namaPegawai = document.getElementById('nama_pegawai').value;
            const errorDiv = document.getElementById('errorMessage');
            const submitBtn = form.querySelector('button[type="submit"]');
            
            // Reset error
            errorDiv.style.display = 'none';
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
            
            try {
                const response = await fetch('{{ route("progres.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        travel_id: travelId,
                        nomor_spt: nomorSpt,
                        nomor_spd: nomorSpd,
                        nama_pegawai: namaPegawai
                    })
                });
                
                const result = await response.json();
                
                if (response.ok && result.success) {
                    showAlert('success', 'Sukses', result.message);
                    
                    // Reset form
                    form.reset();
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addSPTModal'));
                    modal.hide();
                    
                    // Reload page setelah 1 detik
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    errorDiv.textContent = result.message || 'Terjadi kesalahan';
                    errorDiv.style.display = 'block';
                }
            } catch (error) {
                console.error('Error:', error);
                errorDiv.textContent = 'Gagal menyimpan SPT: ' + error.message;
                errorDiv.style.display = 'block';
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Simpan SPT';
            }
        });
    }
});
```

**Features:**
- Event listener on form submit
- Prevents default form submission
- Collects form values
- Shows loading state with spinner
- Sends AJAX POST request with CSRF token
- Handles success response: reset form, close modal, reload page
- Handles error response: displays error message
- Resets button state after operation

---

## 🔄 Data Flow Details

### Request Body (JSON):
```json
{
    "travel_id": 1,
    "nomor_spt": "SPT-2026-001",
    "nomor_spd": "SPD-2026-001",
    "nama_pegawai": "Budi Santoso"
}
```

### Response Success (200 OK):
```json
{
    "success": true,
    "message": "SPT berhasil ditambahkan",
    "data": {
        "id": 123,
        "travel_id": 1,
        "nomor_spt": "SPT-2026-001",
        "nomor_spd": "SPD-2026-001",
        "nama_pegawai": "Budi Santoso",
        "google_drive_folder_id": "1abc...",
        "is_complete": false,
        "created_at": "2026-02-05T10:30:00Z"
    }
}
```

### Response Error (422 Unprocessable Entity):
```json
{
    "success": false,
    "message": "SPT sudah ada dalam daftar"
}
```

---

## 🎨 Styling Details

### Modal Header
- Background: `#2563eb` (Blue)
- Text Color: White
- Close button: White X icon

### Form Fields
- Border Radius: 6px
- Border Color: `#d1d5db` (Light Gray)
- Padding: Standard Bootstrap padding

### Error Message Container
- Display: Initially hidden (`display: none`)
- Background: `#fee2e2` (Light Red)
- Color: `#dc2626` (Red text)
- Padding: `0.75rem`
- Border Radius: 6px

### Buttons
- Primary Button: `#2563eb` (Blue)
- Secondary Button: Bootstrap default gray
- Loading State: Spinner + "Menyimpan..." text

---

## 🔐 Security Implementation

### CSRF Protection:
```html
@csrf  <!-- Added to form -->
```

### CSRF Header:
```javascript
'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
```

### Backend Validation:
```php
// In ProgresController@store
$validated = $request->validate([
    'travel_id' => 'required|exists:travels,id',
    'nomor_spt' => 'required|string',
    'nomor_spd' => 'nullable|string',
    'nama_pegawai' => 'nullable|string',
]);

// Check duplikasi
$exists = SPTProgres::where('travel_id', $validated['travel_id'])
    ->where('nomor_spt', $validated['nomor_spt'])
    ->exists();
```

---

## 🧪 How to Test

### Unit Test Example:
```php
// Test successful SPT creation
$response = $this->post('/progres', [
    'travel_id' => 1,
    'nomor_spt' => 'SPT-2026-001',
    'nomor_spd' => 'SPD-2026-001',
    'nama_pegawai' => 'Test User'
]);

$response->assertStatus(200);
$response->assertJson(['success' => true]);
$this->assertDatabaseHas('spt_progres', ['nomor_spt' => 'SPT-2026-001']);
```

### Manual Test:
1. Open browser DevTools (F12)
2. Go to Network tab
3. Click "Tambah SPT" button
4. Fill form and submit
5. Check Network → POST /progres
6. Verify response JSON
7. Check table updates

---

## 📊 Database Impact

### Before:
```
spt_progres table with some records
travels table with multiple records
```

### After Submit:
```sql
INSERT INTO spt_progres (
    travel_id,
    nomor_spt,
    nomor_spd,
    nama_pegawai,
    google_drive_folder_id,
    google_drive_folder_name,
    is_complete,
    created_at,
    updated_at
) VALUES (...)
```

---

## 🎯 Key Points

✓ Uses existing `ProgresController@store` method
✓ Leverages existing Google Drive integration
✓ Follows Laravel conventions
✓ Bootstrap 5 compatible
✓ Mobile responsive
✓ Accessible (labels, required indicators)
✓ CSRF protected
✓ Error handling on both frontend and backend
✓ Loading states for UX feedback
✓ Auto page reload after success

---

## 📦 Dependencies

- **Laravel:** 12.0+
- **Bootstrap:** 5.3.2
- **Bootstrap Icons:** 1.11.3
- **JavaScript:** ES6+
- **Database:** MySQL with spt_progres table

---

## ✅ Validation Rules

| Field | Rules | Message |
|-------|-------|---------|
| travel_id | required, exists:travels,id | Travel harus dipilih |
| nomor_spt | required, string | Nomor SPT wajib diisi |
| nomor_spt | unique (per travel) | SPT sudah ada dalam daftar |
| nomor_spd | nullable, string | - |
| nama_pegawai | nullable, string | - |

---

## 🔗 Related Files

- `app/Http/Controllers/ProgresController.php` (store method)
- `app/Services/GoogleDriveService.php` (folder creation)
- `routes/web.php` (POST /progres route)
- `app/Models/SPTProgres.php` (model)
- `app/Models/Travel.php` (related model)
