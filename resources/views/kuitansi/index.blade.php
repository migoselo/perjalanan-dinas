@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-1">Kuitansi Perjalanan Dinas</h2>
    <p class="text-muted">Daftar kuitansi perjalanan dinas</p>

    {{-- success flash handled in layout to avoid duplicates --}}

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            @if($travels->count())
                <div class="row">
                @foreach($travels as $travel)
                    <div class="col-md-6">
                        <div class="card mb-3 p-3" style="border-radius:10px;">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $travel->nama_pegawai ?? '-' }}</strong>
                                    <div class="small text-muted">Tanggal: {{ $travel->tanggal_spd ? \Carbon\Carbon::parse($travel->tanggal_spd)->format('d M Y') : '-' }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="small text-muted">Total</div>
                                    <div class="fw-bold text-primary">Rp {{ number_format($travel->grand_total, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <div class="mt-3 d-flex gap-2 flex-wrap">
                                <a href="{{ route('kuitansi.show', $travel->id) }}" class="btn btn-sm btn-outline-info" target="_blank">Lihat</a>
                                <a href="{{ route('kuitansi.inputData', $travel->id) }}" class="btn btn-sm btn-outline-warning">✏️ Input Data</a>
                                <a href="{{ route('kuitansi.pdf', $travel->id) }}" class="btn btn-sm btn-outline-danger" target="_blank">form</a>
                                <a href="{{ route('data.index', ['selected' => $travel->id]) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                            </div>

                        </div>
                    </div>
                @endforeach
                </div>
            @else
                <div class="alert alert-info">Belum ada data kuitansi. <a href="{{ route('travel.create') }}">Buat perjalanan baru</a></div>
            @endif
        </div>
    </div>
</div>

@endsection
