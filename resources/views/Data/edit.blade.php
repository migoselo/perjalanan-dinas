@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Data Perjalanan</h1>

    <form action="{{ route('data.update', $travel) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="spd_number">Nomor SPD</label>
            <input type="text" name="spd_number" id="spd_number" class="form-control" value="{{ old('spd_number', $travel->spd_number) }}" required>
        </div>

        <div class="form-group">
            <label for="date">Tanggal</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', $travel->date) }}" required>
        </div>

        <h3>Transportasi</h3>
        <div id="transport-items">
            @foreach ($travel->transportItems as $index => $item)
                <div class="form-group">
                    <label for="transportItems_{{ $index }}_description">Deskripsi</label>
                    <input type="text" name="transportItems[{{ $index }}][description]" id="transportItems_{{ $index }}_description" class="form-control" value="{{ old("transportItems.$index.description", $item->description) }}">

                    <label for="transportItems_{{ $index }}_amount">Jumlah</label>
                    <input type="number" name="transportItems[{{ $index }}][amount]" id="transportItems_{{ $index }}_amount" class="form-control" value="{{ old("transportItems.$index.amount", $item->amount) }}">

                    <label for="transportItems_{{ $index }}_details">Detail</label>
                    <input type="text" name="transportItems[{{ $index }}][details]" id="transportItems_{{ $index }}_details" class="form-control" value="{{ old("transportItems.$index.details", $item->details) }}">
                </div>
            @endforeach
        </div>

        <h3>Penginapan</h3>
        <div id="accommodation-items">
            @foreach ($travel->accommodationItems as $index => $item)
                <div class="form-group">
                    <label for="accommodationItems_{{ $index }}_description">Deskripsi</label>
                    <input type="text" name="accommodationItems[{{ $index }}][description]" id="accommodationItems_{{ $index }}_description" class="form-control" value="{{ old("accommodationItems.$index.description", $item->description) }}">

                    <label for="accommodationItems_{{ $index }}_amount">Jumlah</label>
                    <input type="number" name="accommodationItems[{{ $index }}][amount]" id="accommodationItems_{{ $index }}_amount" class="form-control" value="{{ old("accommodationItems.$index.amount", $item->amount) }}">

                    <label for="accommodationItems_{{ $index }}_details">Detail</label>
                    <input type="text" name="accommodationItems[{{ $index }}][details]" id="accommodationItems_{{ $index }}_details" class="form-control" value="{{ old("accommodationItems.$index.details", $item->details) }}">
                </div>
            @endforeach
        </div>

        <h3>Uang Harian</h3>
        <div id="perdiem-items">
            @foreach ($travel->perdiemItems as $index => $item)
                <div class="form-group">
                    <label for="perdiemItems_{{ $index }}_description">Deskripsi</label>
                    <input type="text" name="perdiemItems[{{ $index }}][description]" id="perdiemItems_{{ $index }}_description" class="form-control" value="{{ old("perdiemItems.$index.description", $item->description) }}">

                    <label for="perdiemItems_{{ $index }}_amount">Jumlah</label>
                    <input type="number" name="perdiemItems[{{ $index }}][amount]" id="perdiemItems_{{ $index }}_amount" class="form-control" value="{{ old("perdiemItems.$index.amount", $item->amount) }}">

                    <label for="perdiemItems_{{ $index }}_details">Detail</label>
                    <input type="text" name="perdiemItems[{{ $index }}][details]" id="perdiemItems_{{ $index }}_details" class="form-control" value="{{ old("perdiemItems.$index.details", $item->details) }}">
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection