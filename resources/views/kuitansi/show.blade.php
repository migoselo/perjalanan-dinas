<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: A4;
            margin: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            background: white;
            padding: 70px 80px 30px 80px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            .container {
                box-shadow: none;
                margin: 0;
                padding: 70px 80px 30px 80px;
            }
        }
        
        .header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
            position: relative;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            background: transparent;
            border-radius: 50%;
            margin-right: 5px;
            margin-top: -10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .logo-inner {
            width: 45px;
            height: 45px;
            border: 3px solid white;
            border-radius: 50%;
            position: relative;
        }
        
        .logo-wave {
            position: absolute;
            bottom: 10px;
            left: 5px;
            width: 30px;
            height: 15px;
            border-top: 2px solid white;
            border-radius: 50%;
        }
        
        .header-text {
            flex: 1;
        }
        
        .header-text h3 {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 2px;
            line-height: 1.3;
        }
        
        .meta-info {
            text-align: left;
            font-size: 9px;
            position: absolute;
            left: 440px;
            top: 50px;
        }
        
        .meta-info div {
            margin-bottom: 3px;
            display: grid;
            grid-template-columns: 85px 15px 1fr;
        }
        
        .meta-info .meta-label {
            text-align: left;
        }
        
        .meta-info .meta-colon {
            text-align: center;
        }
        
        .meta-info .meta-value {
            text-align: left;
        }
        
        .title-box {
            border: 2px solid black;
            outline: 2px solid black;
            outline-offset: -3px;
            padding: 8px;
            margin: 40px auto -10px auto;
            width: 200px;
            position: relative;
            text-align: center;
        }
        
        .title-box h1 {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 3px;
        }
        
        .separator {
            border-top: 3px solid black;
            margin: 20px 0;
        }
        
        .content {
            margin-top: -10px;
        }
        
        .row {
            display: flex;
            margin-bottom: 10px;
            line-height: 1.6;
        }
        
        .label {
            width: 110px;
            flex-shrink: 0;
            font-size: 9px;
        }
        
        .separator-col {
            margin: 0 10px;
        }
        
        .value {
            flex: 1;
            font-size: 9px;
        }
        
        .amount {
            font-weight: bold;
        }
        
        .terbilang-box {
            border: 2px solid black;
            outline: 2px solid black;
            outline-offset: -3px;
            padding: 8px 12px;
            font-style: italic;
            font-size: 9px;
            text-align: center;
        }
        
        .signature-section {
            margin-top: 80px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            text-align: center;
            width: 45%;
        }
        
        .signature-title {
            font-size: 9px;
            margin-bottom: 80px;
            line-height: 1.5;
        }
        
        .signature-name {
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 3px;
            text-decoration: underline;
        }
        
        .signature-nip {
            font-size: 9px;
            font-weight: bold;
        }
        
        .receiver {
            text-align: right;
            margin-top: 60px;
            font-size: 9px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        
        .receiver-title {
            margin-bottom: 80px;
        }
        
        .receiver-name {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 3px;
            text-align: center;
            font-size: 9px;
        }
        
        .receiver-nip {
            font-weight: bold;
            font-size: 9px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 80px; height: 80px; object-fit: contain;">
            </div>
            <div class="header-text">
                <h3>KEMENTERIAN KOMUNIKASI DAN INFORMATIKA</h3>
                <h3>DIREKTORAT JENDERAL SUMBER DAYA DAN PERANGKAT POS DAN INFORMATIKA</h3>
                <h3>LOKA MONITOR SPEKTRUM FREKUENSI RADIO TANJUNG SELOR</h3>
            </div>
            <div class="meta-info">
                <div><span class="meta-label">Beban MAK</span><span class="meta-colon">:</span><span class="meta-value"><strong>{{ $travel->kode_mak ?? '-' }}</strong></span></div>
                <div><span class="meta-label">Bukti Kas No.</span><span class="meta-colon">:</span><span class="meta-value"><strong>{{ $travel->bukti_kas ?? '-' }}</strong></span></div>
                <div><span class="meta-label">Tahun Anggaran</span><span class="meta-colon">:</span><span class="meta-value"><strong>2026</strong></span></div>
            </div>
        </div>
        
        <div class="title-box">
            <h1>KUITANSI</h1>
        </div>
        
        <div class="separator"></div>
        
        <div class="content">
            <div class="row">
                <div class="label">Sudah terima dari</div>
                <div class="separator-col">:</div>
                <div class="value"><strong>{{ $travel->pemberi_uang ?? 'Pejabat Pembuat Komitmen Loka Monitor Spektrum Frekuensi Radio Tanjung Selor' }}</strong></div>
            </div>
            
            <div class="row">
                <div class="label">Uang sebesar</div>
                <div class="separator-col">:</div>
                <div class="value amount">Rp &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ number_format($travel->grand_total ?? 0, 0, ',', '.') }}</div>
            </div>
            
            <div class="row">
                <div class="label">Untuk pembayaran</div>
                <div class="separator-col">:</div>
                <div class="value">
                    <strong>{{ $travel->uraian_kegiatan ?? '-' }}</strong>
                </div>
            </div>
            
            <div class="row" style="margin-top: 30px;">
                <div class="label">Berdasarkan SPD No.</div>
                <div class="separator-col">:</div>
                <div class="value"><strong>{{ $travel->nomor_spd ?? '-' }}</strong></div>
            </div>
            
            <div class="row" style="margin-bottom: 5px;">
                <div class="label">Terbilang</div>
                <div class="separator-col">:</div>
                <div class="value">
                    <div class="terbilang-box">
                        {{ trim(ucfirst(terbilang($travel->grand_total ?? 0))) }} Rupiah
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 40px; display: flex; justify-content: space-between;">
                <div class="signature-box" style="width: 45%; margin-top: 178px;">
                    <div class="signature-title">Setuju Dibayar,<br>Pejabat Pembuat Komitmen</div>
                    <div class="signature-name">DENI WIJAYANTO</div>
                    <div class="signature-nip">NIP. 198005072006041005</div>
                </div>
                
                <div style="width: 45%; display: flex; flex-direction: column; gap: 60px;">
                    <div class="signature-box" style="width: 100%;">
                        <div class="signature-title">Yang Menerima</div>
                        <div class="signature-name">{{ strtoupper($travel->nama_pegawai ?? '-') }}</div>
                        @php $suratData = $travel->surat_data ?? null; @endphp
                        <div class="signature-nip">NIP. {{ optional($travel->user)->nip ?? $travel->nip ?? ($suratData['nip'] ?? null) ?? '-' }}</div>
                    </div>
                    
                    <div class="signature-box" style="width: 100%;">
                        <div class="signature-title">Setuju dan Lunas dibayar Tanggal, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $travel->tanggal_pembayaran ? \Carbon\Carbon::parse($travel->tanggal_pembayaran)->format('d F Y') : 'Desember 2025' }}<br>Bendahara Pengeluaran</div>
                        <div class="signature-name">ISWANTONO</div>
                        <div class="signature-nip">NIP. 197305062006041004</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>