@extends('layouts.app')

@section('content')
<div style="max-width:1200px;margin:24px auto;padding:18px;background:#fff;border:1px solid #ddd;">
  <h2>{{ isset($travel) ? 'Edit SPD (Surat Perintah Dinas)' : 'Form Input SPD (Surat Perintah Dinas)' }}</h2>

  <form method="post" action="{{ route('spd.preview') }}" style="margin-top:12px;">
    @csrf
    {{-- Show validation errors and success messages --}}
    @if(session('success'))
      <div id="flash-success-spd" style="padding:10px;background:#d4edda;color:#155724;border:1px solid #c3e6cb;margin-bottom:12px;border-radius:4px;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div style="padding:10px;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;margin-bottom:12px;border-radius:4px;">
        <strong>Terjadi kesalahan:</strong>
        <ul style="margin:6px 0 0 18px;">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    @if(isset($travel))
    <input type="hidden" name="travel_id" value="{{ $travel->id }}" />
    @endif

    <!-- Header Info -->
    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px; padding:12px; background:#f5f5f5; border-radius:4px;">
      <div style="flex:1; min-width:200px;">
        <label>Lembar Ke</label>
        <input type="text" name="lembar_ke" id="lembar_ke" class="form-control" value="{{ old('lembar_ke', $travel->spby?->lembar_ke ?? '') }}" />
      </div>
      <div style="flex:1; min-width:200px;">
        <label>Kode No.</label>
        <input type="text" name="kode_no" id="kode_no" class="form-control" value="{{ old('kode_no', $travel->spby?->kode_no ?? '') }}" />
      </div>
      <div style="flex:1; min-width:200px;">
        <label>Nomor SPD <span style="color:red;">*</span></label>
        @if(!empty($travel->nomor_spd))
          <input type="text" class="form-control" value="{{ $travel->nomor_spd }}" disabled style="background:#e9ecef;" />
          <input type="hidden" name="nomor_spd" value="{{ $travel->nomor_spd }}" />
        @else
          <input type="text" name="nomor_spd" class="form-control" value="{{ old('nomor_spd') }}" />
        @endif
      </div>
    </div>

    <!-- 1. Pejabat berwenang -->
    <div style="margin-bottom:15px; padding:12px; background:#f9f9f9; borr-left:3px solid #0047AB;">
      <label style="font-weight:bold; display:block; margin-bottom:8px;">1. Pejabat berwenang yang memberi perintah</label>
      <input type="text" name="pejabat_pemberi_perintah" class="form-control" value="{{ old('pejabat_pemberi_perintah', $travel->spby?->pejabat_pemberi_perintah ?? '') }}" placeholder="Pejabat Pembuat Komitmen Loka MonSpeKFreRad Tanjung Selor" />
    </div>

    <!-- 2. Nama / NIP -->
    <div style="display:flex; gap:12px; margin-bottom:15px;">
      <div style="flex:1.5">
        <label style="font-weight:bold;">2a. Nama Pegawai yang diperintah</label>
        <input type="text" class="form-control" value="{{ $travel->nama_pegawai ?? $travel->spby?->nama_pegawai ?? old('nama_pegawai') }}" disabled />
        <input type="hidden" name="nama_pegawai" value="{{ $travel->nama_pegawai ?? $travel->spby?->nama_pegawai ?? old('nama_pegawai') }}" />
      </div>
      <div style="flex:1">
        <label style="font-weight:bold;">2b. NIP</label>
        <input type="text" class="form-control" value="{{ $travel->nip ?? $travel->spby?->nip_pegawai ?? old('nip_pegawai') }}" disabled />
        <input type="hidden" name="nip_pegawai" value="{{ $travel->nip ?? $travel->spby?->nip_pegawai ?? old('nip_pegawai') }}" />
      </div>
    </div>

    <!-- 3. Pangkat, Jabatan, Tingkat Biaya -->
    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:15px;">
      <div style="flex:1; min-width:250px;">
        <label style="font-weight:bold;">3a. Pangkat / Golongan Ruang Gaji</label>
        <input type="text" name="pangkat" class="form-control" value="{{ old('pangkat', $travel->spby?->pangkat ?? '') }}" placeholder="Penata Muda (IIIa)" />
      </div>
      <div style="flex:1; min-width:250px;">
        <label style="font-weight:bold;">3b. Jabatan / Instansi</label>
        <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $travel->spby?->jabatan ?? '') }}" placeholder="Analis Pengelolaan Keuangan APBN Ahli Pertama" />
      </div>
      <div style="flex:1; min-width:150px;">
        <label style="font-weight:bold;">3c. Tingkat Biaya</label>
        <input type="text" name="tingkat_biaya" class="form-control" value="{{ old('tingkat_biaya', $travel->spby?->tingkat_biaya ?? '') }}" placeholder="A / B / C" />
      </div>
    </div>

    <!-- 4. Maksud Perjalanan Dinas -->
    <div style="margin-bottom:15px;">
      <label style="font-weight:bold;">4. Maksud Perjalanan Dinas</label>
      <textarea name="maksud_perjalanan" rows="3" class="form-control" style="width:100%">{{ old('maksud_perjalanan', $travel->spby?->maksud_perjalanan ?? '') }}</textarea>
    </div>

    <!-- 5. Alat Angkutan -->
    <div style="margin-bottom:15px;">
      <label style="font-weight:bold;">5. Alat angkutan yang dipergunakan</label>
      <input type="text" name="alat_angkutan" class="form-control" value="{{ old('alat_angkutan', $travel->spby?->alat_angkutan ?? '') }}" placeholder="Kendaraan Umum / Pesawat Udara / Kendaraan Dinas" />
    </div>

    <!-- 6. Tempat Berangkat & Tujuan -->
    <div style="display:flex; gap:12px; margin-bottom:15px;">
      <div style="flex:1">
        <label style="font-weight:bold;">6a. Tempat Berangkat</label>
        <input type="text" name="tempat_berangkat" class="form-control" value="{{ old('tempat_berangkat', $travel->spby?->tempat_berangkat ?? '') }}" placeholder="TANJUNG SELOR" />
      </div>
      <div style="flex:1">
        <label style="font-weight:bold;">6b. Tempat Tujuan</label>
        <input type="text" name="tempat_tujuan" class="form-control" value="{{ old('tempat_tujuan', $travel->spby?->tempat_tujuan ?? '') }}" placeholder="BANDUNG" />
      </div>
    </div>

    <!-- 7. Lama, Tanggal Berangkat, Tanggal Kembali -->
    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:15px;">
      <div style="flex:1; min-width:150px;">
        <label style="font-weight:bold;">7a. Lama Perjalanan (Hari)</label>
        <input type="number" name="lama_perjalanan" class="form-control" value="{{ old('lama_perjalanan', $travel->spby?->lama_perjalanan ?? '') }}" placeholder="3" />
      </div>
      <div style="flex:1; min-width:200px;">
        <label style="font-weight:bold;">7b. Tanggal Berangkat</label>
        <input type="date" name="tanggal_berangkat" class="form-control" value="{{ old('tanggal_berangkat', $travel->spby?->tanggal_berangkat ?? '') }}" />
      </div>
      <div style="flex:1; min-width:200px;">
        <label style="font-weight:bold;">7c. Tanggal Harus Kembali</label>
        <input type="date" name="tanggal_kembali" class="form-control" value="{{ old('tanggal_kembali', $travel->spby?->tanggal_kembali ?? '') }}" />
      </div>
    </div>

    <!-- 8. Pengikut -->
    <div style="margin-bottom:15px; padding:12px; background:#f9f9f9; border-left:3px solid #0047AB;">
      <label style="font-weight:bold; display:block; margin-bottom:8px;">8. Pengikut</label>
      @php
        $existingPengikut = [];
        if(old('pengikut_nama')){
          foreach(old('pengikut_nama') as $i => $n){
            $existingPengikut[] = (object)[
              'nama' => $n,
              'tanggal_lahir' => old('pengikut_tgl_lahir')[$i] ?? '',
              'keterangan' => old('pengikut_keterangan')[$i] ?? ''
            ];
          }
        } elseif(isset($travel) && !empty($travel->pengikut)) {
          $existingPengikut = $travel->pengikut;
        }
      @endphp

      <div id="pengikut-container">
        @if(count($existingPengikut))
          @foreach($existingPengikut as $p)
            <div style="display:flex; gap:8px; margin-bottom:8px; align-items:flex-start;">
              <div style="flex:1.5">
                <label style="font-size:8pt;">Nama</label>
                <input type="text" name="pengikut_nama[]" class="form-control" placeholder="Nama pengikut" value="{{ $p->nama ?? $p['nama'] ?? '' }}" />
              </div>
              <div style="flex:1">
                <label style="font-size:8pt;">Tanggal Lahir</label>
                <input type="date" name="pengikut_tgl_lahir[]" class="form-control" value="{{ $p->tanggal_lahir ?? $p['tanggal_lahir'] ?? '' }}" />
              </div>
              <div style="flex:1">
                <label style="font-size:8pt;">Keterangan</label>
                <input type="text" name="pengikut_keterangan[]" class="form-control" value="{{ $p->keterangan ?? $p['keterangan'] ?? '' }}" />
              </div>
              <button type="button" onclick="removePengikut(this)" style="margin-top:20px; padding:6px 12px; background:#dc3545; color:white; border:none; border-radius:4px; cursor:pointer;">Hapus</button>
            </div>
          @endforeach
        @else
          <div style="display:flex; gap:8px; margin-bottom:8px; align-items:flex-start;">
            <div style="flex:1.5">
              <label style="font-size:8pt;">Nama</label>
              <input type="text" name="pengikut_nama[]" class="form-control" placeholder="Nama pengikut" />
            </div>
            <div style="flex:1">
              <label style="font-size:8pt;">Tanggal Lahir</label>
              <input type="date" name="pengikut_tgl_lahir[]" class="form-control" />
            </div>
            <div style="flex:1">
              <label style="font-size:8pt;">Keterangan</label>
              <input type="text" name="pengikut_keterangan[]" class="form-control" />
            </div>
            <button type="button" onclick="removePengikut(this)" style="margin-top:20px; padding:6px 12px; background:#dc3545; color:white; border:none; border-radius:4px; cursor:pointer;">Hapus</button>
          </div>
        @endif
      </div>
      <button type="button" onclick="addPengikut()" style="margin-top:8px; padding:8px 16px; background:#28a745; color:white; border:none; border-radius:4px; cursor:pointer;">+ Tambah Pengikut</button>
    </div>

    <!-- 9. Pembebanan Anggaran -->
    <div style="margin-bottom:15px;">
      <label style="font-weight:bold;">9a. Instansi Pembebanan</label>
      <input type="text" name="instansi_pembebanan" class="form-control" value="{{ old('instansi_pembebanan', $travel->spby?->instansi_pembebanan ?? '') }}" placeholder="Loka Monitor Spektrum Frekuensi Radio Tanjung Selor" />
    </div>
    <div style="margin-bottom:15px;">
      <label style="font-weight:bold;">9b. Mata Anggaran</label>
      <input type="text" name="mata_anggaran" class="form-control" value="{{ old('mata_anggaran', $travel->kode_mak ?? $travel->spby?->mata_anggaran ?? '') }}" placeholder="7437.BAH.078.101.C.524119" />
    </div>

    <!-- 10. Keterangan Lain-lain -->
    <div style="margin-bottom:15px;">
      <label style="font-weight:bold;">10. Keterangan lain-lain</label>
      <textarea name="keterangan" rows="2" class="form-control" style="width:100%">{{ old('keterangan') }}</textarea>
    </div>

    <!-- Signature Section -->
    <div style="margin-top:20px; padding:12px; background:#f5f5f5; border-radius:4px;">
      <h4>Informasi Penandatangan</h4>
      
      <div style="display:flex; gap:12px; flex-wrap:wrap;">
        <div style="flex:1; min-width:200px;">
          <label style="font-weight:bold;">Tempat Penerbitan</label>
          <input type="text" name="tempat_penerbitan" class="form-control" value="{{ old('tempat_penerbitan', $travel->spby?->tempat_penerbitan ?? '') }}" placeholder="Tanjung Selor" />
        </div>
        <div style="flex:1; min-width:200px;">
          <label style="font-weight:bold;">Tanggal Penerbitan</label>
          <input type="date" name="tanggal_penerbitan" class="form-control" value="{{ old('tanggal_penerbitan', $travel->spby?->tanggal_penerbitan ?? '') }}" />
        </div>
      </div>

      <div style="margin-top:20px; font-family: 'Calibri', Times, serif; font-size: 14px; display: flex; justify-content: space-between; gap: 8px;">
        <div style="flex: 1; text-align: center; white-space: nowrap;">
        </div>
        <div style="flex: 1; text-align: center; white-space: nowrap;">
        </div>
        <div style="flex: 1; text-align: center;">
          <div style="white-space: nowrap;">
          </div>
        </div>
      </div>

      <div style="margin-top:5px;">
        <label style="font-weight:bold;">Jabatan Penandatangan</label>
        <input type="text" name="jabatan_penandatangan" class="form-control" value="{{ old('jabatan_penandatangan', $travel->spby?->jabatan_penandatangan ?? '') }}" placeholder="PEJABAT PEMBUAT KOMITMEN" />
      </div>

      <div style="margin-top:12px;">
        <label style="font-weight:bold;">Nama Penandatangan</label>
        <input type="text" name="nama_penandatangan" class="form-control" value="{{ old('nama_penandatangan', $travel->spby?->nama_penandatangan ?? '') }}" placeholder="DENI WIJAYANTO, S.T." />
      </div>

      <div style="margin-top:12px;">
        <label style="font-weight:bold;">NIP Penandatangan</label>
        <input type="text" name="nip_penandatangan" class="form-control" value="{{ old('nip_penandatangan', $travel->spby?->nip_penandatangan ?? '') }}" placeholder="198005072006041005" />
      </div>
    </div>

    <div style="margin-top:20px; display:flex; gap:12px;">
      <button type="submit" style="flex:1; padding:12px; background:#28a745; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:bold; font-size:14pt;">💾 Simpan & Preview / Print SPD</button>
      <a href="{{ route('spd.index') }}" style="flex:1; padding:12px; background:#6c757d; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:bold; font-size:14pt; text-align:center; text-decoration:none;">Kembali</a>
    </div>
  </form>
</div>

<script>
function addPengikut(){
  const container = document.getElementById('pengikut-container');
  const newRow = document.createElement('div');
  newRow.style.cssText = 'display:flex; gap:8px; margin-bottom:8px; align-items:flex-start;';
  newRow.innerHTML = `
    <div style="flex:1.5">
      <input type="text" name="pengikut_nama[]" class="form-control" placeholder="Nama pengikut" />
    </div>
    <div style="flex:1">
      <input type="date" name="pengikut_tgl_lahir[]" class="form-control" />
    </div>
    <div style="flex:1">
      <input type="text" name="pengikut_keterangan[]" class="form-control" />
    </div>
    <button type="button" onclick="removePengikut(this)" style="margin-top:0; padding:6px 12px; background:#dc3545; color:white; border:none; border-radius:4px; cursor:pointer;">Hapus</button>
  `;
  container.appendChild(newRow);
}

function removePengikut(btn){
  btn.parentElement.remove();
}
// Auto-hide flash success message after 5 seconds
setTimeout(function(){
  var el = document.getElementById('flash-success-spd');
  if(el){ el.style.transition = 'opacity 0.5s'; el.style.opacity = 0; setTimeout(function(){ el.remove(); }, 500); }
}, 5000);
</script>
@endsection