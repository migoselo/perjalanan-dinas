@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h3>Detail Perjalanan — #{{ $travel->id }}</h3>
    <div class="mb-3">
        <a href="{{ route('travel.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card card-custom p-3 mb-3">
        <h5>Informasi Umum</h5>
        <p><strong>Nomor SPD:</strong> {{ $travel->nomor_spd }}</p>
        <p><strong>Nama Pegawai:</strong> {{ $travel->nama_pegawai }}</p>
        <p><strong>Tanggal:</strong> {{ optional($travel->tanggal_spd)->format('Y-m-d') ?? '-' }}</p>
        <p><strong>Uraian:</strong> {{ $travel->uraian_kegiatan }}</p>
    </div>

    <div class="card card-custom p-3 mb-3">
        <h5>Transportasi ({{ $travel->transportItems->count() }})</h5>
        <table class="table">
            <thead><tr><th>Mode</th><th>Uraian</th><th class="text-end">Jumlah (Rp)</th></tr></thead>
            <tbody>
            @foreach($travel->transportItems as $t)
                <tr>
                    <td>{{ $t->mode }}</td>
                    <td>{{ $t->description }}</td>
                    <td class="text-end">{{ number_format($t->amount,0,',','.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card card-custom p-3 mb-3">
        <h5>Penginapan ({{ $travel->accommodationItems->count() }})</h5>
        <table class="table">
            <thead><tr><th>Nama</th><th>Lama Hari</th><th class="text-end">Harga (Rp)</th><th class="text-end">Subtotal (Rp)</th></tr></thead>
            <tbody>
            @foreach($travel->accommodationItems as $a)
                <tr>
                    <td>{{ $a->name }}</td>
                    <td>{{ $a->nights }}</td>
                    <td class="text-end">{{ number_format($a->price,0,',','.') }}</td>
                    <td class="text-end">{{ number_format($a->nights * $a->price,0,',','.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card card-custom p-3 mb-3">
        <h5>Uang Harian ({{ $travel->perdiemItems->count() }})</h5>
        <table class="table">
            <thead><tr><th>Kota</th><th>Hari</th><th class="text-end">Rp/hari</th><th class="text-end">Subtotal</th></tr></thead>
            <tbody>
            @foreach($travel->perdiemItems as $p)
                <tr>
                    <td>{{ $p->city }}</td>
                    <td>{{ $p->days }}</td>
                    <td class="text-end">{{ number_format($p->amount,0,',','.') }}</td>
                    <td class="text-end">{{ number_format($p->days * $p->amount,0,',','.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card card-custom p-3">
        <h5>Ringkasan Biaya</h5>
        <p><strong>Total Transport:</strong> Rp {{ number_format($travel->transport_total,0,',','.') }}</p>
        <p><strong>Total Penginapan:</strong> Rp {{ number_format($travel->accommodation_total,0,',','.') }}</p>
        <p><strong>Total Uang Harian:</strong> Rp {{ number_format($travel->perdiem_total,0,',','.') }}</p>
        <hr>
        <p class="fs-5"><strong>Grand Total:</strong> Rp {{ number_format($travel->grand_total,0,',','.') }}</p>
    </div>
</div>
@endsection