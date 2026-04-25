@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; background-color: #f9fafb; padding: 2rem;">
    <div style="max-width: 1280px; margin: 0 auto;">
        <!-- Header Card -->
        <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 2rem; margin-bottom: 2rem;">
            <h1 style="font-size: 2rem; font-weight: bold; color: #1f2937; margin-bottom: 0.5rem;">Sistem Upload Laporan SPT</h1>
            <p style="color: #6b7280; margin-bottom: 1.5rem;">
                Upload <strong>Laporan</strong>, <strong>Penanggung Jawab</strong>, dan <strong>Pembayaran</strong> untuk setiap SPT. File otomatis tersimpan ke Google Drive.
            </p>
            
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
                                <label for="travel_id" class="form-label">Pilih Travel <span style="color: red;">*</span></label>
                                <select class="form-control" id="travel_id" name="travel_id" required style="border-radius: 6px;">
                                    <option value="">-- Pilih Travel --</option>
                                    @foreach($progres as $item)
                                        @if($item->travel)
                                            <option value="{{ $item->travel->id }}" @if($item->travel->id) {{ $item->id ? 'disabled' : '' }} @endif>
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

        <!-- Statistics Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <!-- Total SPT -->
            <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.25rem;">Total SPT</p>
                        <p style="font-size: 2rem; font-weight: bold; color: #1f2937;">{{ $total }}</p>
                    </div>
                    <i class="bi bi-file-text" style="font-size: 2rem; color: #2563eb;"></i>
                </div>
            </div>

            <!-- Sudah Lengkap -->
            <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.25rem;">Sudah Lengkap</p>
                        <p style="font-size: 2rem; font-weight: bold; color: #16a34a;">{{ $complete }}</p>
                    </div>
                    <i class="bi bi-check-circle-fill" style="font-size: 2rem; color: #16a34a;"></i>
                </div>
            </div>

            <!-- Belum Lengkap -->
            <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.25rem;">Belum Lengkap</p>
                        <p style="font-size: 2rem; font-weight: bold; color: #ea580c;">{{ $incomplete }}</p>
                    </div>
                    <i class="bi bi-x-circle-fill" style="font-size: 2rem; color: #ea580c;"></i>
                </div>
            </div>
        </div>
        <!-- Table -->
        <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background-color: #1e40af; color: white;">
                        <tr>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.875rem;">No</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.875rem;">Nama</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.875rem;">Nomor SPT</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.875rem;">Nomor SPD</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; font-size: 0.875rem;">Laporan</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; font-size: 0.875rem;">Penanggung Jawab</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; font-size: 0.875rem;">Pembayaran</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; font-size: 0.875rem;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($progres as $key => $item)
                        @php
                            $rowIndex = (int)($key / 2);
                            $bgColor = ($rowIndex % 2) === 0 ? '#dbeafe' : '#fef3c7';
                        @endphp
                        <tr style="border-bottom: 1px solid #e5e7eb; background-color: {{ $bgColor }};" class="hover-row" data-spt-id="{{ $item->id }}">
                            <td style="padding: 1rem; font-size: 0.875rem;">{{ $key + 1 }}</td>
                            <td style="padding: 1rem; font-size: 0.875rem; font-weight: 500;">{{ $item->travel->nama_pegawai ?? $item->nama_pegawai ?? '-' }}</td>
                            <td style="padding: 1rem; font-size: 0.875rem;">{{ $item->nomor_spt ?? '-' }}</td>
                            <td style="padding: 1rem; font-size: 0.875rem;">{{ $item->travel->nomor_spd ?? $item->nomor_spd ?? '-' }}</td>
                            
                            <!-- Laporan -->
                            <td style="padding: 1rem; text-align: center;" class="file-cell-laporan">
                                @if($item->laporan_file_id)
                                    <i class="bi bi-check-circle-fill" style="color: #16a34a; font-size: 1.5rem;"></i>
                                @else
                                    <!-- FIX: Tambahkan data-file-type attribute -->
                                    <input type="file" 
                                           id="file_{{ $item->id }}_laporan" 
                                           data-spt-id="{{ $item->id }}" 
                                           data-file-type="laporan"
                                           style="display: none;" 
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png" 
                                           onchange="handleFileUpload(event)">
                                    <button type="button" 
                                            onclick="document.getElementById('file_{{ $item->id }}_laporan').click(); return false;" 
                                            style="background-color: #2563eb; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.875rem;">
                                        <i class="bi bi-cloud-upload"></i> Upload
                                    </button>
                                @endif
                            </td>

                            <!-- Penanggung Jawab -->
                            <td style="padding: 1rem; text-align: center;" class="file-cell-penanggung_jawab">
                                @if($item->penanggung_jawab_file_id)
                                    <i class="bi bi-check-circle-fill" style="color: #16a34a; font-size: 1.5rem;"></i>
                                @else
                                    <!-- FIX: Tambahkan data-file-type attribute -->
                                    <input type="file" 
                                           id="file_{{ $item->id }}_penanggung_jawab" 
                                           data-spt-id="{{ $item->id }}" 
                                           data-file-type="penanggung_jawab"
                                           style="display: none;" 
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png" 
                                           onchange="handleFileUpload(event)">
                                    <button type="button" 
                                            onclick="document.getElementById('file_{{ $item->id }}_penanggung_jawab').click(); return false;" 
                                            style="background-color: #a855f7; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.875rem;">
                                        <i class="bi bi-cloud-upload"></i> Upload
                                    </button>
                                @endif
                            </td>

                            <!-- Pembayaran -->
                            <td style="padding: 1rem; text-align: center;" class="file-cell-pembayaran">
                                @if($item->pembayaran_file_id)
                                    <i class="bi bi-check-circle-fill" style="color: #16a34a; font-size: 1.5rem;"></i>
                                @else
                                    <!-- FIX: Tambahkan data-file-type attribute -->
                                    <input type="file" 
                                           id="file_{{ $item->id }}_pembayaran" 
                                           data-spt-id="{{ $item->id }}" 
                                           data-file-type="pembayaran"
                                           style="display: none;" 
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png" 
                                           onchange="handleFileUpload(event)">
                                    <button type="button" 
                                            onclick="document.getElementById('file_{{ $item->id }}_pembayaran').click(); return false;" 
                                            style="background-color: #16a34a; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.875rem;">
                                        <i class="bi bi-cloud-upload"></i> Upload
                                    </button>
                                @endif
                            </td>

                            <!-- Status -->
                            <td style="padding: 1rem; text-align: center;" class="status-cell">
                                @if($item->is_complete)
                                    <span style="background-color: #dcfce7; color: #166534; padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Lengkap</span>
                                @else
                                    <span style="background-color: #fed7aa; color: #b45309; padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Belum Lengkap</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="padding: 2rem; text-align: center; color: #6b7280;">
                                Belum ada data SPT
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


<!-- Link to Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<script>
'use strict';

// ⭐ FIXED: File Upload Handler dengan proper error handling
function handleFileUpload(event) {
    const input = event.target;
    const file = input.files[0];
    
    if (!file) {
        console.warn('❌ No file selected');
        return;
    }

    // ⭐ FIX: Get SPT ID dan file type dari data attributes
    const sptId = input.getAttribute('data-spt-id');
    const fileType = input.getAttribute('data-file-type');
    
    // ⭐ FIX: Validation lebih ketat
    if (!sptId || sptId === 'null' || sptId === 'unknown') {
        showAlert('danger', 'Error', 'SPT ID tidak ditemukan. Silakan refresh halaman.');
        console.error('❌ Invalid SPT ID:', sptId);
        input.value = ''; // Reset file input
        return;
    }
    
    if (!fileType) {
        showAlert('danger', 'Error', 'Tipe file tidak valid');
        console.error('❌ Invalid file type');
        input.value = ''; // Reset file input
        return;
    }

    console.log('📤 Upload starting...', { sptId, fileType, fileName: file.name });

    // Get parent row for UI updates
    const row = input.closest('tr');
    const fileCell = row ? row.querySelector(`.file-cell-${fileType}`) : null;
    
    if (fileCell) {
        fileCell.innerHTML = `
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; color: #2563eb;">
                <div class="spinner-border spinner-border-sm"></div>
                <span style="font-size: 0.875rem;">Uploading...</span>
            </div>
        `;
    }

    // ⭐ FIX: Create FormData dengan CSRF token yang benar
    const formData = new FormData();
    formData.append('file', file);
    formData.append('file_type', fileType);
    // CSRF token sudah ada di meta tag, akan diambil di header

    // Upload ke server
    const uploadUrl = `/progres/${sptId}/upload`;
    
    fetch(uploadUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        credentials: 'same-origin', // ⭐ IMPORTANT: Include cookies/session
        body: formData
    })
    .then(response => {
        console.log('📡 Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('📡 Response data:', data);
        
        if (data.success) {
            showAlert('success', 'Berhasil', 'File berhasil diupload ke Google Drive!');
            
            // Update UI - Tampilkan checkmark
            if (fileCell) {
                fileCell.innerHTML = `
                    <i class="bi bi-check-circle-fill" style="color: #16a34a; font-size: 1.5rem; animation: popIn 0.3s ease;"></i>
                `;
            }
            
            // Update status jika semua file sudah lengkap
            checkAndUpdateStatus(row);
            
            // Reset input
            input.value = '';
        } else {
            throw new Error(data.message || 'Upload gagal');
        }
    })
    .catch(error => {
        console.error('❌ Upload error:', error);
        showAlert('danger', 'Error', 'Upload gagal: ' + error.message);
        
        // Kembalikan tombol upload
        if (fileCell) {
            const buttonColor = fileType === 'laporan' ? '#2563eb' : 
                              fileType === 'penanggung_jawab' ? '#a855f7' : '#16a34a';
            fileCell.innerHTML = `
                <input type="file" 
                       id="file_${sptId}_${fileType}" 
                       data-spt-id="${sptId}" 
                       data-file-type="${fileType}"
                       style="display: none;" 
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png" 
                       onchange="handleFileUpload(event)">
                <button type="button" 
                        onclick="document.getElementById('file_${sptId}_${fileType}').click();" 
                        style="background-color: ${buttonColor}; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.875rem;">
                    <i class="bi bi-cloud-upload"></i> Upload
                </button>
            `;
        }
        
        // Reset input
        input.value = '';
    });
}

// Check and update completion status
function checkAndUpdateStatus(row) {
    if (!row) return;
    
    const laporan = row.querySelector('.file-cell-laporan .bi-check-circle-fill');
    const penanggungJawab = row.querySelector('.file-cell-penanggung_jawab .bi-check-circle-fill');
    const pembayaran = row.querySelector('.file-cell-pembayaran .bi-check-circle-fill');
    
    const statusCell = row.querySelector('.status-cell');
    if (!statusCell) return;
    
    if (laporan && penanggungJawab && pembayaran) {
        statusCell.innerHTML = `
            <span style="background-color: #dcfce7; color: #166534; padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; animation: popIn 0.3s ease;">
                Lengkap
            </span>
        `;
    }
}

// Show alert function
function showAlert(type, title, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    alertDiv.innerHTML = `
        <strong>${title}:</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

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

// Initialize table state
function initializeTableState() {
    const rows = document.querySelectorAll('tbody tr[data-spt-id]');
    console.log(`📊 Found ${rows.length} SPT rows`);
    
    rows.forEach((row, index) => {
        const sptId = row.getAttribute('data-spt-id');
        console.log(`Row ${index + 1}: SPT ID = ${sptId}`);
    });
}

// Search functionality
const searchInput = document.getElementById('searchSPT');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr[data-spt-id]');
        
        rows.forEach(row => {
            const nama = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const nomorSpt = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            
            if (nama.includes(searchTerm) || nomorSpt.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

// ⭐ PAGE INITIALIZATION
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Page loaded, initializing...');
    initializeTableState();
});

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes popIn {
        from {
            opacity: 0;
            transform: scale(0.5);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.2em;
    }
    
    .spinner-border {
        display: inline-block;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #2563eb;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .hover-row:hover {
        filter: brightness(0.95);
    }
`;
document.head.appendChild(style);
</script>
@endsection