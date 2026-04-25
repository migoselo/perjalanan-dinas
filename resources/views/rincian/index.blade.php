<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rincian Biaya Perjalanan Dinas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
        }
        .list-group-item.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="container-fluid">
        <h2 class="mb-1">Daftar Perjalanan Dinas</h2>
        <p class="text-muted">Total {{ $travels->count() }} data perjalanan dinas</p>

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
                                        <strong>{{ $travel->nama_pegawai ?? '-' }}</strong>
                                        <div class="small text-muted">No. SPD: {{ $travel->nomor_spd ?? '-' }}</div>
                                        <div class="small text-muted">Tanggal: {{ optional($travel->tanggal_spd)->format('Y-m-d') ?? '-' }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="small text-muted">Total</div>
                                        <div class="fw-bold text-primary">Rp {{ number_format($grand,0,',','.') }}</div>
                                    </div>
                                </div>

                                <div class="mt-3 d-flex gap-2">
                                    <a href="{{ route('rincian.form', ['travel' => $travel->id]) }}" class="btn btn-sm btn-warning">✎ Isi Data Surat</a>
                                    <a href="{{ route('surat.index', ['travelId' => $travel->id]) }}" class="btn btn-sm btn-outline-primary" target="_blank">Lihat Surat</a>
                                    <a href="{{ route('data.index', ['selected' => $travel->id]) }}" class="btn btn-sm btn-outline-secondary">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                @else
                    <div class="alert alert-info">Belum ada data perjalanan dinas</div>
                @endif
            </div>
        </div>
    </div>
    @endsection
</body>
</html>