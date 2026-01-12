@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-1">Form Input Perjalanan Dinas</h2>
    <p class="text-muted">Silakan lengkapi data perjalanan dinas Anda</p>

    <form action="{{ route('travel.store') }}" method="POST">
        @csrf

        {{-- A. Identitas (partial sederhana, bisa tetap dipisah) --}}
        @include('form_input.partials.identitas')

        {{-- B. Transportasi --}}
        @include('form_input.partials.transportasi')

        {{-- C. Penginapan --}}
        @include('form_input.partials.penginapan')

        {{-- D. Uang Harian (bisa dibuat partial juga jika perlu) --}}
        @include('form_input.partials.uang_harian')

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
        </div>
    </form>
</div>
@endsection