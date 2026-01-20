<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>SPBY - {{ $travel->destination ?? 'Travel' }}</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
      font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
      color: #333;
      background: #f5f5f5;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    .preview-section {
      margin-top: 40px;
    }

    .preview-section h2 {
      font-size: 18px;
      color: #2c3e50;
      margin-bottom: 20px;
      text-align: center;
    }

    .a4-page {
      width: 210mm;
      min-height: 297mm;
      margin: 15mm auto;
      padding: 12mm;
      box-sizing: border-box;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }

    .spby {
      width: 100%;
      max-width: 180mm;
      margin: 0 auto;
      border: 2px solid #000;
      padding: 18px;
      box-sizing: border-box;
      position: relative;
      background: #fff;
      font-family: Calibri, Arial, sans-serif;
      font-size: 12px;
    }

    .header {
      display: flex;
      align-items: center;
      gap: 12px;
      position: relative;
      min-height: 110px;
    }

    .logo {
      width: 100px;
      flex: 0 0 125px;
    }

    .logo img {
      display: block;
      max-width: 100%;
    }

    .org {
      position: absolute;
      left: 55%;
      transform: translateX(-48%);
      top: 25px;
      text-align: center;
      font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
      width: 520px;
      line-height: 1.5;
    }

    .title {
      text-align: center;
      margin-top: 5px;
      margin-bottom: 6px;
      font-weight: 900;
      font-size: 12px;
      text-decoration: underline;
    }

    .meta {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 8px;
      font-size: 14px;
    }

    .meta td {
      padding: 4px 6px;
      vertical-align: top;
    }

    .divider {
      border-top: 3px solid #000;
      margin: 0 -18px 8px;
    }

    .amount-box {
      border: 1px solid #000;
      padding: 10px;
      margin: 10px 0;
      background: #f9f9f9;
    }

    .amount-row {
      display: flex;
      gap: 0px;
      align-items: center;
    }

    .amount-row .label {
      font-weight: 700;
      font-size: 14px;
      margin-right: 3px;
    }

    .amount-row .amount {
      font-size: 18px;
      font-weight: 700;
    }

    .terbilang {
      border-top: 1px solid #000;
      padding-top: 6px;
      margin-top: 6px;
      font-style: italic;
      font-size: 14px;
    }

    .signs {
      display: flex;
      justify-content: space-between;
      margin-top: 5px;
      gap: 12px;
    }

    .sign {
      width: 32%;
      text-align: center;
    }

    .sign .role {
      font-size: 14px;
    }

    .sign .name {
      margin-top: 110px;
      text-decoration: underline;
      font-size: 14px;
    }

    .sign .nip {
      margin-top: 6px;
      font-size: 14px;
    }

    .print-btn {
      position: fixed;
      right: 20px;
      top: 20px;
      z-index: 999;
      padding: 10px 16px;
      background: #27ae60;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
      font-size: 14px;
    }

    .print-btn:hover {
      background: #229954;
    }

    @media print {
      * {
        margin: 0;
        padding: 0;
      }

      body {
        background: #fff !important;
        margin: 0;
        padding: 0;
        font-family: Calibri, Arial, sans-serif;
      }

      .container {
        max-width: 100%;
        margin: 0;
        padding: 0;
      }

      .preview-section {
        margin-top: 0;
      }

      .preview-section h2,
      .print-btn {
        display: none !important;
      }

      .a4-page {
        width: 210mm;
        height: 297mm;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        page-break-after: always;
        background: #fff;
      }

      .spby {
        width: 100%;
        max-width: 100%;
        margin: 0;
        border: 2px solid #000;
        padding: 18px;
        box-sizing: border-box;
        background: #fff;
        font-family: Calibri, Arial, sans-serif;
        font-size: 12px;
      }

      .header {
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        min-height: 110px;
      }

      .logo {
        width: 100px;
        flex: 0 0 125px;
      }

      .logo img {
        display: block;
        max-width: 100%;
      }

      .org {
        position: absolute;
        left: 55%;
        transform: translateX(-48%);
        top: 25px;
        text-align: center;
        font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
        width: 520px;
        line-height: 1.5;
      }

      table {
        border-collapse: collapse;
      }

      p, div, span {
        orphans: 3;
        widows: 3;
      }
    }
  </style>
</head>
<body>
  <button class="print-btn" onclick="window.print()">🖨️ Print</button>

  <div class="container">
    <!-- Preview Section -->
    <div class="preview-section">
      <div class="a4-page">
        <div class="spby">
          <div class="header">
            <div class="logo">
              <img src="{{ asset('images/logo.png') }}" alt="logo" onerror="this.style.display='none'">
            </div>

            <div class="org">
              <div style="font-size:12px;"><strong style="font-weight: 700;">KEMENTERIAN KOMUNIKASI DAN INFORMATIKA</strong></div>
              <div style="font-size:12px;"><strong style="font-weight: 700;">DIREKTORAT JENDERAL SUMBER DAYA DAN PERANGKAT POS DAN INFORMATIKA</strong></div>
              <div class="title" style="font-size:12px; font-weight: 500;"><strong>LOKA MONITOR SPEKTRUM FREKUENSI RADIO TANJUNG SELOR</strong></div>
            </div>
          </div>

          <div class="title" style="font-size:16px; margin-top: -20px; font-weight: 500;">
          <strong><u>SURAT PERINTAH BAYAR</u></strong>
            </div>

          <table class="meta">
            <tr>
              <td style="width:50%; text-align:center;">Tanggal : {{ $spbyRecord?->tanggal_spby?->format('d F Y') ?? '___' }}</td>
              <td style="width:50%; text-align:center;">No. : {{ $spbyRecord?->nomor_spby ? $spbyRecord->nomor_spby . '/SPBY/' . $spbyRecord->tanggal_spby?->format('F Y') : '___/SPBY/___' }}</td>
            </tr>
          </table>

          <div class="divider"></div>

          <p style="font-size:14px;">Saya yang bertanda tangan dibawah ini selaku Pejabat Pembuat Komitmen memerintahkan Bendahara Pengeluaran agar melakukan pembayaran sejumlah :</p>

          <div class="amount-box">
            <div class="amount-row">
              <div class="label">Rp.</div>
              <div class="amount">{{ number_format($spbyRecord?->jumlah_pembayaran ?? $grandTotal ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="terbilang">
              Terbilang : {{ $spbyRecord?->amount_in_words ?? $terbilang ?? 'Nol Rupiah' }}
            </div>
          </div>

          <table style="width:100%; margin-top:8px; font-family: Calibri, Arial; font-size:14px; border-collapse:collapse; table-layout:fixed;">
            <colgroup>
              <col style="width: 120px;">
              <col style="width: 15px;">
              <col style="width: auto;">
            </colgroup>
            <tr>
              <td style="padding: 2px 0; white-space: nowrap; padding-right: 8px; vertical-align: top;">Kepada</td>
              <td style="padding: 2px 0; white-space: nowrap; vertical-align: top;">:</td>
              <td style="padding: 2px 0; padding-left:3px; vertical-align: top; word-wrap: break-word;">{{ $spbyRecord?->recipient_name ?? 'NABILAH' }}</td>
            </tr>
            <tr>
              <td style="padding: 2px 0; white-space: nowrap; padding-right: 8px; vertical-align: top;">Untuk pembayaran</td>
              <td style="padding: 2px 0; white-space: nowrap; vertical-align: top;">:</td>
              <td style="padding: 2px 0; padding-left:3px; vertical-align: top; word-wrap: break-word;">{{ $spbyRecord?->keterangan ?? 'Perjalanan Dinas' }}</td>
            </tr>
          </table>

          <div style="margin-top:6px; font-family: Calibri, Arial; font-size:14px;">
            <div style="margin-bottom:4px; margin-top:20px;">Atas dasar</div>
            <ol style="margin: 0 0 0 20px; padding: 0;">
              <li style="margin-bottom:4px;">Kuitansi / bukti pembelian : -</li>
              <li style="margin-bottom:4px;">Nota / bukti penerimaan barang/ :<br style="margin: 0; padding: 0;">jasa / bukti lainnya</li>
            </ol>
          </div>

          <div style="margin-top:30px; font-family: Calibri, Arial; font-size:14px;">
            <div style="margin-bottom:2px;">Dibebankan pada;</div>
            <table style="width:100%; border-collapse:collapse;">
              <tr>
                <td style="padding: 2px 0; width:130px;">Kegiatan, output, MAK</td>
                <td style="padding: 2px 0; width:10px;">:</td>
                <td style="padding: 2px 0; padding-left:3px;">{{ $spbyRecord?->activity_mak ?? '7437.BAH.078.101.C.524119' }}</td>
              </tr>
              <tr>
                <td style="padding: 2px 0;">Kode</td>
                <td style="padding: 2px 0; width:10px;">:</td>
                <td style="padding: 2px 0; padding-left:3px;">{{ $spbyRecord?->code ?? '-' }}</td>
              </tr>
            </table>
          </div>

          <div class="divider" style="margin-top:20px;"></div>

          <div style="font-family: 'Calibri', Times, serif; font-size: 14px; margin-bottom: 8px; margin-top: 25px; display: flex; justify-content: space-between; gap: 8px; margin-left: -10px;">
            <div style="flex: 1; text-align: center; white-space: nowrap;">
              <span style="display: inline;">Setuju/Lunas dibayar, tanggal,</span>
              <span style="margin-left: 2px; display: inline;">{{ now()->format('d F Y') }}</span>
            </div>
            <div style="flex: 1; text-align: center; white-space: nowrap;">
              <span style="display: inline;">Diterima tanggal,</span>
              <span style="margin-left: 2px; display: inline;">{{ now()->format('d F Y') }}</span>
            </div>
            <div style="flex: 1; text-align: center;">
              <div style="white-space: nowrap; display: flex; flex-direction: column; align-items: center;">
                <div>
                  <span style="display: inline;">Tanjung Selor,</span>
                  <span style="margin-left: 2px; display: inline;">{{ now()->format('d F Y') }}</span>
                </div>
                <div style="font-size: 14px; margin-top: 10px;">
                  a.n. Kuasa Pengguna Anggaran
                </div>
              </div>
            </div>
          </div>

          <div class="signs">
            @foreach($signatories as $sig)
            <div class="sign">
              <div class="role">{{ $sig['position'] ?? '' }}</div>
              <div class="name">{{ $sig['name'] ?? '' }}</div>
              <div class="nip">NIP. {{ $sig['nip'] ?? '' }}</div>
            </div>
            @endforeach
          </div>

        </div>
      </div>

    </div>
  </div>

  <!-- Auto-reload listener for postMessage from form -->
  <script>
    window.addEventListener('message', function(event) {
      // Listen for SPBY_DATA_UPDATED message from form.blade.php
      if (event.data.type === 'SPBY_DATA_UPDATED') {
        // Reload page to show updated data from database
        location.reload();
      }
    });
  </script>
</body>
</html>
