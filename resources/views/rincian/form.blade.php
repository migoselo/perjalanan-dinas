@extends('layouts.app')

@section('content')
<div class="container mt-4">
  <h1 class="text-center mb-4">📋 Form Input Data Surat Perjalanan Dinas</h1>

  <!-- Success Message -->
  <div class="alert alert-success alert-dismissible fade" id="successMessage" role="alert" style="display: none;">
    ✓ Data surat berhasil disimpan! Redirecting...
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>

  <!-- Error Message -->
  <div class="alert alert-danger alert-dismissible fade" id="errorMessage" role="alert" style="display: none;">
    <span id="errorText"></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>

  <!-- Form Section -->
  <div class="card">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">📝 Input Data Surat</h5>
    </div>
    <div class="card-body">
      <div class="alert alert-info">
        <strong>ℹ️ Informasi:</strong><br>
        Isi form di bawah dengan tanggal surat dan data lainnya. Data akan tersimpan secara permanen.
      </div>

      <form id="suratForm" method="POST" action="{{ route('surat.store') }}">
        @csrf

        <!-- Hidden Travel ID -->
        <input type="hidden" name="travel_id" value="{{ $travel->id }}">

        <div class="row">
          <!-- Input Tanggal Surat -->
          <div class="col-md-6 mb-3">
            <label for="tanggal_surat" class="form-label fw-bold">Tanggal Surat *</label>
            <input type="date" id="tanggal_surat" name="tanggal_surat" class="form-control" 
               value="{{ old('tanggal_surat') }}" required onchange="updatePreview()">
            <small class="text-muted">Tanggal penerbitan surat</small>
          </div>

          <!-- Input Tanggal SPD -->
          <div class="col-md-6 mb-3">
            <label for="tanggal_spd" class="form-label fw-bold">Tanggal SPD</label>
            <input type="date" id="tanggal_spd" name="tanggal_spd" class="form-control"
                 value="{{ old('tanggal_spd') ?? (is_string($travel->tanggal_spd) ? $travel->tanggal_spd : $travel->tanggal_spd?->format('Y-m-d')) ?? '' }}" onchange="updatePreview()">
            <small class="text-muted">Tanggal Surat Perintah Dinas</small>
          </div>
        </div>

        <!-- Button Group -->
        <div class="d-flex gap-2 justify-content-center mt-4">
          <button type="button" class="btn btn-primary px-4" onclick="submitSuratForm(event)">
            <i class="bi bi-check"></i> Simpan
          </button>
          <button type="reset" class="btn btn-secondary px-4" onclick="resetForm()">
            <i class="bi bi-arrow-clockwise"></i> Reset
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Submit form dengan AJAX
  function submitSuratForm(event) {
    event.preventDefault();
    
    const form = document.getElementById('suratForm');
    
    // Validasi tanggal surat
    const tanggalSurat = document.getElementById('tanggal_surat').value;
    if (!tanggalSurat) {
      showError('Tanggal Surat harus diisi!');
      return;
    }
    
    // Buat FormData dari form
    const formData = new FormData(form);
    
    console.log('Form data:', Object.fromEntries(formData));
    
    // Kirim AJAX dengan fetch
    fetch('{{ route("surat.store") }}', {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      body: formData
    })
    .then(response => {
      console.log('Response status:', response.status);
      if (!response.ok) {
        return response.text().then(text => {
          throw new Error(`HTTP ${response.status}: ${text}`);
        });
      }
      return response.json();
    })
    .then(data => {
      console.log('Response data:', data);
      if (data.success) {
        document.getElementById('successMessage').style.display = 'block';
        document.getElementById('successMessage').classList.add('show');
        setTimeout(() => {
          console.log('Redirecting to:', data.redirect);
          window.location.href = data.redirect;
        }, 1500);
      } else {
        showError(data.message || 'Terjadi kesalahan');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showError('Gagal menyimpan data: ' + error.message);
    });
  }

  // Tampilkan error message
  function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    errorText.textContent = message;
    errorDiv.style.display = 'block';
    errorDiv.classList.add('show');
    setTimeout(() => {
      errorDiv.classList.remove('show');
      errorDiv.style.display = 'none';
    }, 5000);
  }

  // Reset form
  function resetForm() {
    document.getElementById('suratForm').reset();
    document.getElementById('errorMessage').style.display = 'none';
    document.getElementById('errorMessage').classList.remove('show');
    document.getElementById('successMessage').style.display = 'none';
    document.getElementById('successMessage').classList.remove('show');
  }

  // Update preview (untuk keperluan real-time)
  function updatePreview() {
    // Placeholder jika ada preview section
  }
</script>
@endsection