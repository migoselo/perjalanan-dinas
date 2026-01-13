<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>SPBY - {{ $spby['number'] ?? $spby->number ?? 'SPBY' }}</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <style>
    body { font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; color:#000; }
    .spby { width: 900px; margin: 20px auto; border: 2px solid #000; padding: 10px; box-sizing: border-box; }
    .header { display:flex; align-items:center; gap:12px; }
    .logo { width:100px; }
    .org { text-align:center; flex:1; font-family: 'Times New Roman', Times, serif; }
    .org h2 { margin:0; font-size:14px; letter-spacing:0.5px; }
    .org .sub { font-size:13px; margin-top:6px; font-weight:700; }
    .org .sub.loka { text-decoration:underline; font-weight:900; }
    .title { text-align:center; margin-top:8px; margin-bottom:6px; font-weight:700; font-size:16px; text-decoration:underline; }
    .meta { width:100%; border-collapse:collapse; margin-bottom:8px; }
    .meta td { padding:4px 6px; vertical-align:top; }
    .amount-box { border:1px solid #000; padding:8px; margin:8px 0; background:#f9f9f9; }
    .amount-row { display:flex; gap:12px; align-items:center; }
    .amount-row .label { font-weight:700; width:120px; }
    .terbilang { border-top:1px solid #000; padding-top:6px; margin-top:6px; font-style:italic; }
    .section { margin:8px 0; }
    .two-col { display:flex; gap:12px; }
    .col { flex:1; }
    .box { border:1px solid #000; padding:10px; min-height:120px; }
    .small { font-size:12px; }
    .signs { display:flex; justify-content:space-between; margin-top:18px; }
    .sign { width:30%; text-align:center; }
    .sign .name { margin-top:60px; font-weight:700; text-decoration:underline; }
    .sign .nip { margin-top:6px; font-size:12px; }
    .print-btn { position:fixed; right:20px; top:20px; }
    @media print {
      .print-btn { display:none; }
      body { margin:0; }
      .spby { border:1px solid #000; width:100%; }
    }
  </style>
</head>
<body>
  <button class="print-btn" onclick="window.print()">🖨️ Print SPBY</button>

  <div class="spby">
    <div class="header">
      <div class="logo">
        {{-- Optional: put logo at public/images/logo.png --}}
        <img src="{{ asset('images/logo.png') }}" alt="logo" style="max-width:100%;" onerror="this.style.display='none'">
      </div>
      <div class="org">
        <div style="font-size:14px;font-weight:900">KEMENTERIAN KOMUNIKASI DAN INFORMATIKA</div>
        <div class="sub" style="font-weight:900">DIREKTORAT JENDERAL SUMBER DAYA DAN PERANGKAT POS DAN INFORMATIKA</div>
        <div class="sub loka" style="margin-top:6px">LOKA MONITOR SPEKTRUM FREKUENSI RADIO TANJUNG SELOR</div>
      </div>
    </div>

    <div class="title">SURAT PERINTAH BAYAR</div>

    <table class="meta">
      <tr>
        <td style="width:60%; text-align:center; font-size:11px;">Tanggal : {{ $spby['date'] ?? $spby->date ?? 'Desember 2025' }}</td>
        <td style="width:40%; text-align:center; font-size:11px;">No. : {{ $spby['number'] ?? $spby->number ?? '/SPBY/ Desember 2025' }}</td>
      </tr>
    </table>
    <div style="border-top:3px solid #000; margin-bottom:8px;"></div>

    <p class="small" style="font-size:14px;">Saya yang bertanda tangan dibawah ini selaku Pejabat Pembuat Komitmen memerintahkan Bendahara Pengeluaran agar melakukan pembayaran sejumlah :</p>

    <div class="amount-box">
        <div class="amount-row">
            <div class="label">Rp.</div>
            <div class="amount" style="font-size:18px; font-weight:700;">
            {{ $spby['amount'] ?? $spby->amount ?? '7.634.146,-' }}
            </div>
      </div>
      <div class="terbilang">
        Terbilang : {{ $spby['amount_in_words'] ?? $spby->amount_in_words ?? 'Tujuh Juta Enam Ratus Tiga Puluh Empat Ribu Seratus Empat Puluh Enam Rupiah' }}
      </div>
    </div>

    <div class="section">
      <div style="margin-bottom:6px; font-family:'Times New Roman', Times, serif; font-size:16px; display:flex; gap:0;">
        <span style="white-space:nowrap; width:160px; text-align:right;">Kepada :</span>
        <span style="margin-left:8px;">{{ $spby['recipient_name'] ?? $spby->recipient_name ?? 'NABHILAH' }}</span>
      </div>
    </div>

    <div class="section">
      <div style="margin-bottom:6px; display:flex; gap:0; font-family:'Times New Roman', Times, serif; font-size:16px">
        <span style="white-space:nowrap; width:160px; text-align:right;">Untuk pembayaran :</span>
        <span style="margin-left:8px;">{!! nl2br(e($spby['purpose'] ?? $spby->purpose ?? "Perjalanan Dinas dari Tanjung Selor ke Bandung selama 3 hari pada tanggal 22 Desember 2025 s/d 24 Desember 2025 dalam rangka Menghadiri Sosialisasi Standar Biaya Masukan Tahun 2026 di Bandung sesuai dengan SPD nomor: 507/SPD/LOKMON.65/XII/2025 tanggal 15 Desember 2025")) !!}</span>
      </div>
    </div>

    <div style="margin-top:8px">
      <div class="small" style="margin-bottom:8px;">
        <div style="margin-bottom:6px">Atas dasar</div>
        <ol style="margin:0 0 0 18px; padding:0">
          <li>Kuintansi / bukti pembelian : -</li>
          <li>Nota / bukti penerimaan barang / jasa / bukti lainnya : -</li>
        </ol>
      </div>

      <div class="small">
        <div style="margin-bottom:6px">Dibebankan pada;</div>
        <div>
          Kegiatan, output, MAK :<br>
          {{ $spby['activity_mak'] ?? $spby->activity_mak ?? '7437.BAH.078.101.C.524119' }}<br>
          <div style="margin-top:8px">Kode : {{ $spby['code'] ?? $spby->code ?? '-' }}</div>
        </div>
      </div>
    </div>

    <div style="border-top:2px solid #000; margin:15px 0;"></div>

    <div style="margin-top:12px; font-size:12px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <div style="width:33%; text-align:left;">
          Setuju/Lunas dibayar, tanggal, <br>
          <strong>{{ $spby['date'] ?? $spby->date ?? 'Desember 2025' }}</strong><br>
          Bendahara Pengeluaran
        </div>
        <div style="width:33%; text-align:center;">
          Diterima tanggal, <br>
          <strong>{{ $spby['date'] ?? $spby->date ?? 'Desember 2025' }}</strong><br>
          Penerima Uang/Uang Muka Kerja
        </div>
        <div style="width:33%; text-align:right;">
          Tanjung Selor, <br>
          <strong>{{ $spby['date'] ?? $spby->date ?? 'Desember 2025' }}</strong><br>
          a.n. Kuasa Pengguna Anggaran <br>
          Pejabat Pembuat Komitmen
        </div>
      </div>
    </div>

    <div class="signs">
      @php
        $signs = $spby['signatures'] ?? $spby->signatures ?? [
          ['role'=>'Bendahara Pengeluaran','name'=>'ISWANTONO','nip'=>'197305062006041004'],
          ['role'=>'Penerima Uang/Uang Muka Kerja','name'=>'NABHILAH','nip'=>'200109252025042001'],
          ['role'=>'Pejabat Pembuat Komitmen','name'=>'DENI WIJAYANTO','nip'=>'198005072006041005'],
        ];
      @endphp

      @foreach($signs as $s)
      <div class="sign">
        <div class="small">{{ $s['role'] ?? $s->role ?? '' }}</div>
        <div class="name">{{ $s['name'] ?? $s->name ?? '' }}</div>
        <div class="nip">NIP. {{ $s['nip'] ?? $s->nip ?? '' }}</div>
      </div>
      @endforeach
    </div>

  </div>
</body>
</html>