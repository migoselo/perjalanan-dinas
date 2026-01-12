<div class="card card-custom p-4 mb-4">
    <h5 class="section-title">A. Identitas Pegawai</h5>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="label-number">1. Nomor Surat Perjalanan Dinas</label>
            <input type="text" name="nomor_spd" value="{{ old('nomor_spd') }}" class="form-control form-control-custom" placeholder="Masukkan nomor SPD">
        </div>

        <div class="col-md-6 mb-3">
            <label class="label-number">2. Nomor Surat Tugas</label>
            <input type="text" name="nomor_surat_tugas" value="{{ old('nomor_surat_tugas') }}" class="form-control form-control-custom" placeholder="Masukkan nomor surat tugas">
        </div>

        <div class="col-md-6 mb-3">
            <label class="label-number">3. Tanggal SPD dan SPT</label>
            <input type="date" name="tanggal_spd" value="{{ old('tanggal_spd') }}" class="form-control form-control-custom">
        </div>

        <div class="col-md-6 mb-3">
            <label class="label-number">4. Sumber Dana</label>
            <input type="text" name="sumber_dana" value="{{ old('sumber_dana') }}" class="form-control form-control-custom" placeholder="Contoh: APBN, APBD, dll">
        </div>

        <div class="col-md-6 mb-3">
            <label class="label-number">5. Kode MAK</label>
            <input type="text" name="kode_mak" value="{{ old('kode_mak') }}" class="form-control form-control-custom" placeholder="Masukkan kode MAK">
        </div>

        <div class="col-md-6 mb-3">
            <label class="label-number">6. Nama Pegawai</label>
            <input type="text" name="nama_pegawai" value="{{ old('nama_pegawai') }}" class="form-control form-control-custom" placeholder="Masukkan nama lengkap">
        </div>

        <div class="col-md-6 mb-3">
            <label class="label-number">7. Bukti Kas</label>
            <input type="text" name="bukti_kas" value="{{ old('bukti_kas') }}" class="form-control form-control-custom" placeholder="Nomor bukti kas">
        </div>

        <div class="col-12 mb-3">
            <label class="label-number">8. Uraian Kegiatan</label>
            <textarea name="uraian_kegiatan" rows="3" class="form-control form-control-custom" placeholder="Jelaskan kegiatan perjalanan dinas">{{ old('uraian_kegiatan') }}</textarea>
        </div>
    </div>
</div>