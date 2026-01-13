<?php

namespace App\Http\Controllers;

use App\Models\Travel;
use Illuminate\Http\Request;

class TravelController extends Controller
{
    public function index()
    {
        return redirect()->route('data.index');
    }
}
?>
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-1">Surat Perintah Bayar (SPBY)</h2>
    <p class="text-muted">Daftar SPBY berdasarkan data perjalanan dinas</p>

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
                                <a href="{{ route('data.spby', $travel) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat SPBY</a>
                                <a href="{{ route('data.spby.pdf', $travel) }}" class="btn btn-sm btn-primary">Unduh PDF</a>
                                <a href="{{ route('data.index', ['selected' => $travel->id]) }}" class="btn btn-sm btn-outline-secondary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            @else
                <div class="alert alert-info">Belum ada data SPBY</div>
            @endif
        </div>
    </div>
</div>
@endsection