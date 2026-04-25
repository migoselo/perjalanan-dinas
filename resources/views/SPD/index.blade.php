@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-1">Surat Perintah Perjalanan Dinas (SPD)</h2>
    <p class="text-muted">Daftar SPD berdasarkan data perjalanan dinas</p>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
                    @php
                        $transportTotal = $travel->transportItems->sum('amount');
                        $hotelTotal = $travel->accommodationItems->sum(function($i){ return $i->nights * $i->price; });
                        $perdiemTotal = $travel->perdiemItems->sum(function($p){ return $p->days * $p->amount; });
                        $grand = $transportTotal + $hotelTotal + $perdiemTotal;
                    @endphp

                    <div class="col-md-6">
                        <div class="card mb-3 p-3" style="border-radius:10px;">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $travel->spby?->nama_pegawai ?? $travel->nama_pegawai ?? '-' }}</strong>
                                    <div class="small text-muted">No. SPD: {{ $travel->nomor_spd ?? '-' }}</div>
                                    <div class="small text-muted">Tanggal: {{ optional($travel->spby?->tanggal_penerbitan)->format('Y-m-d') ?? optional($travel->tanggal_spd)->format('Y-m-d') ?? '-' }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="small text-muted">Total</div>
                                    <div class="fw-bold text-primary">Rp {{ number_format($grand,0,',','.') }}</div>
                                </div>
                            </div>

                            <div class="mt-3 d-flex gap-2">
                                <a href="{{ route('spd.show', $travel->id) }}" class="btn btn-sm btn-outline-info" target="_blank">👁️ Lihat SPD</a>
                                <a href="{{ route('spd.edit', $travel->id) }}" class="btn btn-sm btn-outline-warning">✏️ Edit SPD</a>
                                <a href="{{ route('data.index', ['selected' => $travel->id]) }}" class="btn btn-sm btn-outline-secondary">📋 Detail</a>
                            </div>

                        </div>
                    </div>
                @endforeach
                </div>
            @else
                <div class="alert alert-info">Belum ada data SPD</div>
            @endif
        </div>
    </div>
</div>

@endsection
