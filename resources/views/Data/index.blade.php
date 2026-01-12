@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-1">Data Perjalanan Dinas</h2>
    <p class="text-muted">Total {{ $travels->total() }} data perjalanan dinas</p>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <h5 class="mb-3">Daftar Perjalanan Dinas</h5>

            @foreach($travels as $travel)
            @php
                $transportTotal = $travel->transportItems->sum('amount');
                $hotelTotal = $travel->accommodationItems->sum(function($i){ return $i->nights * $i->price; });
                $perdiemTotal = $travel->perdiemItems->sum(function($p){ return $p->days * $p->amount; });
                $grand = $transportTotal + $hotelTotal + $perdiemTotal;
            @endphp

            <div class="card mb-3 p-3 list-card" data-id="{{ $travel->id }}" data-url="{{ route('data.partial', $travel) }}" style="border-radius:12px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">{{ $travel->nama_pegawai ?? '—' }}</h6>
                        <div class="small text-muted">No. SPD: {{ $travel->nomor_spd ?? '-' }}</div>
                        <div class="small text-muted">Tanggal: {{ optional($travel->tanggal_spd)->format('Y-m-d') ?? '-' }}</div>
                        <div class="small text-muted">Disubmit: {{ $travel->created_at->format('d F Y \p\u\k\u\l H.i') }}</div>
                    </div>

                    <div class="text-end">
                        <div class="small text-muted">Total Biaya</div>
                        <div class="fw-bold text-primary">Rp {{ number_format($grand,0,',','.') }}</div>
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <div class="p-3" style="background:#f6f7f8;border-radius:8px;min-width:130px;">
                        <div class="small text-muted">Transportasi</div>
                        <div class="fw-bold">Rp {{ number_format($transportTotal,0,',','.') }}</div>
                    </div>

                    <div class="p-3" style="background:#f6f7f8;border-radius:8px;min-width:130px;">
                        <div class="small text-muted">Penginapan</div>
                        <div class="fw-bold">Rp {{ number_format($hotelTotal,0,',','.') }}</div>
                    </div>

                    <div class="p-3 ms-auto" style="background:#f6f7f8;border-radius:8px;min-width:130px;">
                        <div class="small text-muted">Uang Harian</div>
                        <div class="fw-bold">Rp {{ number_format($perdiemTotal,0,',','.') }}</div>
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <a href="{{ route('data.index', ['selected' => $travel->id]) }}" class="btn btn-sm btn-outline-primary open-link me-auto">Lihat</a>
                    <button class="btn btn-sm btn-outline-danger delete-btn" data-action="{{ route('data.destroy', $travel) }}">Hapus</button>
                </div>
            </div>
            @endforeach

            <div class="mt-3">
                {{ $travels->appends(request()->query())->links() }}
            </div>
        </div>

        <div class="col-lg-6">
            <h5 class="mb-3">Detail Perjalanan Dinas</h5>

            <div id="detail-panel">
                @if($selected)
                    @include('data._detail', ['travel' => $selected])
                @else
                    <div class="card card-custom p-4">
                        <div class="text-muted">Pilih salah satu data di kiri untuk melihat detail</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
.list-card.active { box-shadow: 0 6px 18px rgba(13,17,26,0.06); border:1px solid #00000010; background:#fff;}
.list-card { cursor:pointer; transition:transform .06s; }
.list-card:hover { transform: translateY(-2px); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const selectedId = params.get('selected');

    function markActive(id) {
        document.querySelectorAll('.list-card').forEach(c => c.classList.remove('active'));
        const el = document.querySelector('.list-card[data-id="'+id+'"]');
        if (el) el.classList.add('active');
    }

    if (selectedId) markActive(selectedId);

    document.querySelectorAll('.list-card').forEach(card => {
        card.addEventListener('click', function (e) {
            if (e.target.closest('.delete-btn')) return;

            const url = card.getAttribute('data-url');
            const id = card.getAttribute('data-id');
            if (!url) return;

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
              .then(r => r.text())
              .then(html => {
                  document.getElementById('detail-panel').innerHTML = html;
                  markActive(id);
                  const newUrl = new URL(window.location.href);
                  newUrl.searchParams.set('selected', id);
                  history.pushState({}, '', newUrl);
              })
              .catch(err => console.error(err));
        });
    });

    document.addEventListener('click', function(e){
        const del = e.target.closest('.delete-btn');
        if (!del) return;
        e.stopPropagation();
        if (!confirm('Hapus data ini?')) return;

        const action = del.getAttribute('data-action');
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = action;
        form.innerHTML = `@csrf @method('DELETE')`;
        document.body.appendChild(form);
        form.submit();
    });
});
</script>
@endpush