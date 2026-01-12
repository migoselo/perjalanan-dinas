<div class="card card-custom p-4">
    <div class="mb-3">
        <h6>Informasi Umum</h6>
        <p class="mb-1"><strong>Nomor SPD:</strong> {{ $travel->nomor_spd ?? '-' }}</p>
        <p class="mb-1"><strong>Nama Pegawai:</strong> {{ $travel->nama_pegawai ?? '-' }}</p>
        <p class="mb-1"><strong>Tanggal:</strong> {{ optional($travel->tanggal_spd)->format('Y-m-d') ?? '-' }}</p>
        <p class="mb-0"><strong>Uraian:</strong> {{ $travel->uraian_kegiatan ?? '-' }}</p>
    </div>

    <hr>

    <div class="mb-3">
        <h6>Transportasi ({{ $travel->transportItems->count() }})</h6>
        @if($travel->transportItems->count())
        <table class="table table-sm">
            <thead class="table-light"><tr><th>Mode</th><th>Uraian</th><th class="text-end">Jumlah</th></tr></thead>
            <tbody>
            @foreach($travel->transportItems as $t)
                <tr>
                    <td>{{ $t->mode }}</td>
                    <td class="text-truncate" style="max-width:150px;">{{ $t->description }}</td>
                    <td class="text-end">Rp {{ number_format($t->amount,0,',','.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <p class="text-muted small">Tidak ada data transportasi</p>
        @endif
    </div>

    <hr>

    <div class="mb-3">
        <h6>Penginapan ({{ $travel->accommodationItems->count() }})</h6>
        @if($travel->accommodationItems->count())
        <table class="table table-sm">
            <thead class="table-light"><tr><th>Nama</th><th>Hari</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr></thead>
            <tbody>
            @foreach($travel->accommodationItems as $a)
                <tr>
                    <td class="text-truncate" style="max-width:120px;">{{ $a->name }}</td>
                    <td>{{ $a->nights }}</td>
                    <td class="text-end">Rp {{ number_format($a->price,0,',','.') }}</td>
                    <td class="text-end">Rp {{ number_format($a->nights * $a->price,0,',','.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <p class="text-muted small">Tidak ada data penginapan</p>
        @endif
    </div>

    <hr>

    <div class="mb-3">
        <h6>Uang Harian ({{ $travel->perdiemItems->count() }})</h6>
        @if($travel->perdiemItems->count())
        <table class="table table-sm">
            <thead class="table-light"><tr><th>Kota</th><th>Hari</th><th class="text-end">Rp/hari</th><th class="text-end">Subtotal</th></tr></thead>
            <tbody>
            @foreach($travel->perdiemItems as $p)
                <tr>
                    <td>{{ $p->city }}</td>
                    <td>{{ $p->days }}</td>
                    <td class="text-end">Rp {{ number_format($p->amount,0,',','.') }}</td>
                    <td class="text-end">Rp {{ number_format($p->days * $p->amount,0,',','.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <p class="text-muted small">Tidak ada data uang harian</p>
        @endif
    </div>

    <hr>

    <div>
        <h6>Ringkasan Biaya</h6>
        <div class="row">
            <div class="col-6">
                <p class="mb-1"><strong>Transport:</strong> <span class="float-end">Rp {{ number_format($travel->transport_total,0,',','.') }}</span></p>
                <p class="mb-1"><strong>Penginapan:</strong> <span class="float-end">Rp {{ number_format($travel->accommodation_total,0,',','.') }}</span></p>
                <p class="mb-3"><strong>Uang Harian:</strong> <span class="float-end">Rp {{ number_format($travel->perdiem_total,0,',','.') }}</span></p>
                <p class="border-top pt-2"><strong>Grand Total:</strong> <span class="float-end fw-bold text-success">Rp {{ number_format($travel->grand_total,0,',','.') }}</span></p>
            </div>
        </div>
    </div>

    <!-- somewhere in resources/views/data/_detail.blade.php -->
<div class="mb-3 d-flex gap-2">
    <a href="{{ route('data.spby', $travel) }}" target="_blank" class="btn btn-outline-primary">Lihat SPBY</a>
    <a href="{{ route('data.spby.pdf', $travel) }}" class="btn btn-primary">Unduh PDF</a>
</div>
</div>
