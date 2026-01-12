<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>SPBY - {{ $travel->id }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body { font-family: "DejaVu Sans", "Arial", sans-serif; color: #000; font-size:12px; }
        .container { padding: 18px; }
        .header { text-align:center; position:relative; }
        .logo { position:absolute; left:30px; top:20px; width:70px; }
        .header h2 { margin:0; font-size:14px; }
        .box { border:1px solid #000; padding:10px; margin-top:8px; }
        table { width:100%; border-collapse: collapse; }
        .right { text-align:right; }
        .center { text-align:center; }
        .signature { width:33%; display:inline-block; text-align:center; vertical-align:top; margin-top:40px; }
        .small { font-size:11px; color:#333; }
        .field-row { margin-bottom:6px; }
        .bold { font-weight:700; }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        {{-- pastikan file logo ada di public/images/logo.png --}}
        <img src="{{ public_path('images/logo.png') }}" alt="logo" class="logo">
        <h3>{{ config('spby.org.name') }}</h3>
        <div class="small">{{ config('spby.org.subname') }}</div>
        <div style="font-weight:700; margin-top:6px;">{{ config('spby.org.unit') }}</div>
        <hr style="border:1px solid #000; margin-top:10px;">
    </div>

    <div class="center" style="margin-top:6px;">
        <div style="font-weight:700; margin-bottom:6px;">SURAT PERINTAH BAYAR</div>
        <div class="small">Tanggal : {{ now()->format('F Y') }} &nbsp;&nbsp; No. : /SPBY/{{ now()->format('F Y') }}</div>
    </div>

    <div class="box" style="margin-top:12px;">
        <div class="field-row">
            <div>Saya yang bertanda tangan dibawah ini selaku Pejabat Pembuat Komitmen memerintahkan Bendahara Pengeluaran agar melakukan pembayaran sejumlah : </div>
        </div>

        <div style="margin:8px 0; font-size:16px; font-weight:700;">
            Rp {{ number_format($grandTotal,0,',','.') }}
        </div>

        <div style="background:#f4f4f4; padding:8px; border-radius:4px; margin-bottom:8px;">
            <em>{{ $terbilang }}</em>
        </div>

        <table>
            <tr>
                <td style="width:80px; vertical-align:top;">Kepada</td>
                <td style="vertical-align:top; width:10px;">:</td>
                <td style="vertical-align:top;">
                    <strong>{{ $signatories['penerima']['name'] }}</strong><br>
                    Untuk pembayaran:<br>
                    {{-- isi uraian dari travel --}}
                    {{ $travel->uraian_kegiatan ?? '-' }}
                    <br>
                    <small class="small">No. SPD: {{ $travel->nomor_spd ?? '-' }} &nbsp; | &nbsp; Tanggal: {{ optional($travel->tanggal_spd)->format('Y-m-d') ?? '-' }}</small>
                </td>
            </tr>
        </table>

        <div style="margin-top:10px;">
            <strong>Atas dasar</strong>
            <ol>
                <li>Kuitansi / bukti pembelian</li>
                <li>Nota / bukti penerimaan barang / jasa / bukti lainnya</li>
            </ol>
        </div>

        <div style="margin-top:8px;">
            <strong>Dibebankan pada:</strong> Kegiatan, output, MAK : {{ $travel->kode_mak ?? '-' }}
        </div>
    </div>

    <div style="margin-top:14px;">
        <div style="display:flex; justify-content:space-between;">
            <div style="width:33%; text-align:center;">
                <div class="small">Setuju/Lunas dibayar, tanggal</div>
                <div style="height:60px;"></div>
                <div class="bold">{{ $signatories['bendahara']['name'] }}</div>
                <div class="small">NIP. {{ $signatories['bendahara']['nip'] }}</div>
            </div>

            <div style="width:33%; text-align:center;">
                <div class="small">Diterima tanggal</div>
                <div style="height:60px;"></div>
                <div class="bold">{{ $signatories['penerima']['name'] }}</div>
                <div class="small">NIP. {{ $signatories['penerima']['nip'] }}</div>
            </div>

            <div style="width:33%; text-align:center;">
                <div class="small">Tanjung Selor, {{ now()->format('F Y') }}</div>
                <div style="height:60px;"></div>
                <div class="bold">{{ $signatories['ppk']['name'] }}</div>
                <div class="small">NIP. {{ $signatories['ppk']['nip'] }}</div>
            </div>
        </div>
    </div>

</div>
</body>
</html>