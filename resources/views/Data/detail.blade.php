<div class="card card-custom p-4">
    <h5 class="mb-3">#{{ $travel->id }} — {{ $travel->nama_pegawai }}</h5>

    <div class="mb-4">
        <h6>A. Identitas Pegawai</h6>
        <dl class="row">
            <dt class="col-sm-4 text-muted">Nomor SPD:</dt><dd class="col-sm-8">{{ $travel->nomor_spd }}</dd>
            <dt class="col-sm-4 text-muted">Nomor Surat Tugas:</dt><dd class="col-sm-8">{{ $travel->nomor_surat_tugas }}</dd>
            <dt class="col-sm-4 text-muted">Tanggal:</dt><dd class="col-sm-8">{{ optional($travel->tanggal_spd)->format('Y-m-d') ?? '-' }}</dd>
            <dt class="col-sm-4 text-muted">Sumber Dana:</dt><dd class="col-sm-8">{{ $travel->sumber_dana }}</dd>
            <dt class="col-sm-4 text-muted">Kode MAK:</dt><dd class="col-sm-8">{{ $travel->kode_mak }}</dd>
            <dt class="col-sm-4 text-muted">Nama Pegawai:</dt><dd class="col-sm-8">{{ $travel->nama_pegawai }}</dd>
            <dt class="col-sm-4 text-muted">Bukti Kas:</dt><dd class="col-sm-8">{{ $travel->bukti_kas }}</dd>
            <dt class="col-sm-4 text-muted">Uraian Kegiatan:</dt><dd class="col-sm-8">{{ $travel->uraian_kegiatan }}</dd>
        </dl>
    </div>

    <div class="mb-3">
        <h6>B. Transportasi</h6>
        @foreach($travel->transportItems as $t)
        <div class="p-3 mb-2" style="background:#f8f9fa;border-radius:8px;">
            <div><strong>Jenis:</strong> {{ $t->mode }}</div>
            <div><strong>Uraian:</strong> {{ $t->description }}</div>
            <div><strong>Jumlah:</strong> Rp {{ number_format($t->amount,0,',','.') }}</div>
        </div>
        @endforeach
    </div>

    <div class="mb-3">
        <h6>C. Penginapan</h6>
        @foreach($travel->accommodationItems as $a)
        <div class="p-3 mb-2" style="background:#f8f9fa;border-radius:8px;">
            <div><strong>Nama:</strong> {{ $a->name }}</div>
            <div><strong>Lama Hari:</strong> {{ $a->nights }}</div>
            <div><strong>Harga (Rp):</strong> {{ number_format($a->price,0,',','.') }}</div>
            <div><strong>Subtotal:</strong> Rp {{ number_format($a->nights * $a->price,0,',','.') }}</div>
        </div>
        @endforeach
    </div>

    <div class="mb-3">
        <h6>D. Uang Harian</h6>
        @foreach($travel->perdiemItems as $p)
        <div class="p-3 mb-2" style="background:#f8f9fa;border-radius:8px;">
            <div><strong>Kota:</strong> {{ $p->city }}</div>
            <div><strong>Hari:</strong> {{ $p->days }}</div>
            <div><strong>Rp/hari:</strong> {{ number_format($p->amount,0,',','.') }}</div>
            <div><strong>Subtotal:</strong> Rp {{ number_format($p->days * $p->amount,0,',','.') }}</div>
        </div>
        @endforeach
    </div>

    <div class="mt-3">
        <h6>Ringkasan</h6>
        <p><strong>Total Transport:</strong> Rp {{ number_format($travel->transport_total,0,',','.') }}</p>
        <p><strong>Total Penginapan:</strong> Rp {{ number_format($travel->accommodation_total,0,',','.') }}</p>
        <p><strong>Total Uang Harian:</strong> Rp {{ number_format($travel->perdiem_total,0,',','.') }}</p>
        <hr>
        <p class="fs-5"><strong>Grand Total:</strong> Rp {{ number_format($travel->grand_total,0,',','.') }}</p>
    </div>
</div>