<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>Form SPBY - {{ $travel->destination ?? 'Travel' }}</title>
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

    h1 {
      color: #2c3e50;
      margin-bottom: 20px;
      text-align: center;
    }

    .form-section {
      background: white;
      padding: 30px;
      border-radius: 8px;
      margin-bottom: 30px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }

    .form-section h2 {
      font-size: 18px;
      color: #2c3e50;
      margin-bottom: 20px;
      border-bottom: 2px solid #3498db;
      padding-bottom: 10px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      font-weight: bold;
      color: #2c3e50;
      margin-bottom: 8px;
      font-size: 14px;
      display: block;
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #bdc3c7;
      border-radius: 4px;
      font-family: Arial, sans-serif;
      font-size: 14px;
      transition: border-color 0.3s;
      box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #3498db;
      box-shadow: 0 0 4px rgba(52, 152, 219, 0.2);
    }

    .info-box {
      background: #e8f4f8;
      border-left: 4px solid #3498db;
      padding: 12px;
      margin-bottom: 20px;
      border-radius: 4px;
      font-size: 13px;
      color: #2c3e50;
    }

    .info-box strong {
      display: block;
      margin-bottom: 6px;
    }

    .button-group {
      display: flex;
      gap: 10px;
      justify-content: center;
      flex-wrap: wrap;
      margin-top: 25px;
    }

    .btn {
      padding: 12px 24px;
      border: none;
      border-radius: 4px;
      font-size: 14px;
      font-weight: bold;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s;
    }

    .btn-primary {
      background: #3498db;
      color: white;
    }

    .btn-primary:hover {
      background: #2980b9;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .btn-primary:active {
      transform: translateY(0);
    }

    .btn-info {
      background: #2ecc71;
      color: white;
    }

    .btn-info:hover {
      background: #27ae60;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .btn-secondary {
      background: #95a5a6;
      color: white;
    }

    .btn-secondary:hover {
      background: #7f8c8d;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    small {
      display: block;
      color: #7f8c8d;
      margin-top: 5px;
      font-size: 12px;
    }

    .success-message {
      display: none;
      background: #d4edda;
      border: 1px solid #c3e6cb;
      color: #155724;
      padding: 12px;
      border-radius: 4px;
      margin-bottom: 20px;
      text-align: center;
    }

    .success-message.show {
      display: block;
    }

    .error-message {
      display: none;
      background: #f8d7da;
      border: 1px solid #f5c6cb;
      color: #721c24;
      padding: 12px;
      border-radius: 4px;
      margin-bottom: 20px;
    }

    .error-message.show {
      display: block;
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

      .form-section,
      .success-message,
      .error-message,
      h1,
      .preview-section h2 {
        display: none !important;
      }

      .preview-section {
        margin-top: 0;
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
  <div class="container">
    <!-- Success Message -->
    <div class="success-message" id="successMessage">
      ✓ SPBY berhasil disimpan!
    </div>

    <!-- Error Message -->
    <div class="error-message" id="errorMessage"></div>

    <h1>📋 Form SPBY - {{ $travel->destination ?? 'Travel' }}</h1>

    <div class="form-section">
      <h2>📝 Input Data SPBY</h2>

      <div class="info-box">
        <strong>ℹ️ Informasi:</strong>
        Isi form di bawah untuk membuat Surat Perintah Bayar. Preview akan ditampilkan di halaman terpisah.
      </div>

      <form id="spbyForm" action="{{ route('spby.store', $travel) }}" method="POST">
        @csrf

        <div class="form-group">
          <label for="date">Tanggal SPBY *</label>
          <input type="date" id="date" name="tanggal_spby" value="{{ $formData['date'] }}" required>
          <small>Tanggal penerbitan Surat Perintah Bayar</small>
        </div>

        <div class="form-group">
          <label for="number">Nomor SPBY *</label>
          <input type="text" id="number" name="nomor_spby" placeholder="Contoh: 507" value="{{ $formData['number'] }}" required>
          <small>Format: [nomor], bulan & tahun otomatis</small>
        </div>

        <div class="form-group">
          <label for="purpose">Untuk Pembayaran *</label>
          <input type="text" id="purpose" name="untuk_pembayaran" placeholder="Contoh: Perjalanan Dinas ke destinasi" value="{{ $formData['purpose'] ?? '' }}" required>
          <small>Deskripsi pembayaran</small>
        </div>

        <div class="form-group">
          <label for="amount">Jumlah Pembayaran (Rp) *</label>
          <input type="text" id="amount" name="jumlah_pembayaran" placeholder="Contoh: 7.634.146" value="{{ number_format($formData['amount'] ?? 0, 0, ',', '.') }}" required>
          <small>Dari Data atau input manual</small>
        </div>

        <div class="button-group">
          <button type="button" class="btn btn-primary" onclick="submitSpbyForm(event)">✓ Simpan</button>
          <a href="{{ route('data.spby', $travel) }}" target="_blank" class="btn btn-info">👁️ Lihat Preview</a>
          <a href="{{ route('data.index') }}" class="btn btn-secondary">← Kembali</a>
        </div>
      </form>
    </div>

    <!-- Preview Section -->
    <div class="preview-section">
      <h2>👁️ Preview SPBY Real-Time</h2>

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
              <td style="width:50%; text-align:center;">Tanggal : <span id="preview-date">___</span></td>
              <td style="width:50%; text-align:center;">No. : <span id="preview-number">___</span></td>
            </tr>
          </table>

          <div class="divider"></div>

          <p style="font-size:14px;">Saya yang bertanda tangan dibawah ini selaku Pejabat Pembuat Komitmen memerintahkan Bendahara Pengeluaran agar melakukan pembayaran sejumlah :</p>

          <div class="amount-box">
            <div class="amount-row">
              <div class="label">Rp.</div>
              <div class="amount" id="preview-amount">___</div>
            </div>
            <div class="terbilang">
              Terbilang : <span id="preview-terbilang">___</span>
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
              <td style="padding: 2px 0; padding-left:3px; vertical-align: top; word-wrap: break-word;">NABILAH</td>
            </tr>
            <tr>
              <td style="padding: 2px 0; white-space: nowrap; padding-right: 8px; vertical-align: top;">Untuk pembayaran</td>
              <td style="padding: 2px 0; white-space: nowrap; vertical-align: top;">:</td>
              <td style="padding: 2px 0; padding-left:3px; vertical-align: top; word-wrap: break-word;"><span id="preview-purpose">Perjalanan Dinas</span></td>
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
                <td style="padding: 2px 0; padding-left:3px;">7437.BAH.078.101.C.524119</td>
              </tr>
              <tr>
                <td style="padding: 2px 0;">Kode</td>
                <td style="padding: 2px 0; width:10px;">:</td>
                <td style="padding: 2px 0; padding-left:3px;">-</td>
              </tr>
            </table>
          </div>

          <div class="divider" style="margin-top:20px;"></div>

          <div style="font-family: 'Calibri', Times, serif; font-size: 14px; margin-bottom: 8px; margin-top: 25px; display: flex; justify-content: space-between; gap: 8px; margin-left: -40px;">
            <div style="flex: 1; text-align: center; white-space: nowrap;">
              <span style="display: inline;">Setuju/Lunas dibayar, tanggal,</span>
              <span style="margin-left: 2px; display: inline;" id="date-1">Desember 2025</span>
            </div>
            <div style="flex: 1; text-align: center; white-space: nowrap;">
              <span style="display: inline;">Diterima tanggal,</span>
              <span style="margin-left: 2px; display: inline;" id="date-2">Desember 2025</span>
            </div>
            <div style="flex: 1; text-align: center;">
              <div style="white-space: nowrap; display: flex; flex-direction: column; align-items: center;">
                <div>
                  <span style="display: inline;">Tanjung Selor,</span>
                  <span style="margin-left: 2px; display: inline;" id="date-3">Desember 2025</span>
                </div>
                <div style="font-size: 14px; margin-top: 10px;">
                  a.n. Kuasa Pengguna Anggaran
                </div>
              </div>
            </div>
          </div>

          <div class="signs">
            <div class="sign">
              <div class="role">Bendahara Pengeluaran</div>
              <div class="name">ISWANTONO</div>
              <div class="nip">NIP. 197305062006041004</div>
            </div>
            <div class="sign">
              <div class="role">Penerima Uang/Uang Muka Kerja</div>
              <div class="name">NABILAH</div>
              <div class="nip">NIP. 200109252025042001</div>
            </div>
            <div class="sign">
              <div class="role">Pejabat Pembuat Komitmen</div>
              <div class="name">DENI WIJAYANTO</div>
              <div class="nip">NIP. 198005072006041005</div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>

  <script>
    // Format number to Rp currency with thousands separator
    function formatRp(inputValue) {
      let num = inputValue.toString().replace(/\D/g, '');
      return new Intl.NumberFormat('id-ID').format(num);
    }

    // Parse Rp formatted number back to integer
    function parseRp(value) {
      return parseInt(value.replace(/\D/g, '') || 0);
    }

    // Convert number to Indonesian words (terbilang)
    function terbilang(n) {
      if (n === 0) return 'Nol';
      
      const satuan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
      const belasan = ['Sepuluh', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas', 
                       'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
      const puluhan = ['', '', 'Dua Puluh', 'Tiga Puluh', 'Empat Puluh', 'Lima Puluh', 
                       'Enam Puluh', 'Tujuh Puluh', 'Delapan Puluh', 'Sembilan Puluh'];
      
      function konversi(num) {
        if (num === 0) return '';
        else if (num < 10) return satuan[num];
        else if (num < 20) return belasan[num - 10];
        else if (num < 100) {
          return puluhan[Math.floor(num / 10)] + (num % 10 !== 0 ? ' ' + satuan[num % 10] : '');
        } else if (num < 1000) {
          return satuan[Math.floor(num / 100)] + ' Ratus' + (num % 100 !== 0 ? ' ' + konversi(num % 100) : '');
        }
        return '';
      }

      const skala = ['', 'Ribu', 'Juta', 'Miliar', 'Triliun'];
      let result = [];
      let idx = 0;
      
      while (n > 0) {
        let chunk = n % 1000;
        if (chunk !== 0) {
          let part = konversi(chunk);
          if (idx > 0) part += ' ' + skala[idx];
          result.unshift(part);
        }
        n = Math.floor(n / 1000);
        idx++;
      }
      
      return result.join(' ').trim();
    }

    // Update preview when form changes
    function updatePreview() {
      const date = document.getElementById('date').value;
      const number = document.getElementById('number').value;
      const amountInput = document.getElementById('amount').value;
      const purpose = document.getElementById('purpose').value || 'Perjalanan Dinas';

      if (!date) {
        document.getElementById('preview-date').textContent = '___';
        document.getElementById('preview-number').textContent = '___/SPBY/___';
        document.getElementById('date-1').textContent = 'Desember 2025';
        document.getElementById('date-2').textContent = 'Desember 2025';
        document.getElementById('date-3').textContent = 'Desember 2025';
      } else {
        const dateObj = new Date(date + 'T00:00:00');
        
        // Format date: "19 January 2026"
        const formatter = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        const dateFormatted = formatter.format(dateObj);
        
        // Extract month and year: "January 2026"
        const monthYearFormatter = new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' });
        const monthYear = monthYearFormatter.format(dateObj);
        
        // Format nomor: "507/SPBY/January 2026"
        const nomor = number.trim() ? `${number}/SPBY/${monthYear}` : `___/SPBY/${monthYear}`;
        
        document.getElementById('preview-date').textContent = dateFormatted;
        document.getElementById('preview-number').textContent = nomor;
        
        // Update signature dates
        const day = dateObj.getDate();
        const signatureDate = `${day} ${monthYear}`;
        document.getElementById('date-1').textContent = signatureDate;
        document.getElementById('date-2').textContent = signatureDate;
        document.getElementById('date-3').textContent = signatureDate;
      }

      // Update amount and terbilang
      const amount = parseRp(amountInput);
      if (amount > 0) {
        document.getElementById('preview-amount').textContent = `${formatRp(amount)},-`;
        document.getElementById('preview-terbilang').textContent = `${terbilang(amount)} Rupiah`;
      } else {
        document.getElementById('preview-amount').textContent = '___';
        document.getElementById('preview-terbilang').textContent = '___';
      }

      // Update purpose
      document.getElementById('preview-purpose').textContent = purpose;
    }

    // Add event listeners for real-time update
    document.getElementById('date').addEventListener('change', updatePreview);
    document.getElementById('number').addEventListener('input', updatePreview);
    document.getElementById('purpose').addEventListener('input', updatePreview);
    
    document.getElementById('amount').addEventListener('blur', function() {
      this.value = formatRp(this.value);
      updatePreview();
    });

    document.getElementById('amount').addEventListener('focus', function() {
      this.value = parseRp(this.value);
    });

    document.getElementById('amount').addEventListener('input', updatePreview);

    // Format input field on blur
    document.getElementById('amount').addEventListener('blur', function() {
      this.value = formatRp(this.value);
    });

    // Clean input on focus
    document.getElementById('amount').addEventListener('focus', function() {
      this.value = parseRp(this.value);
    });

    // Submit SPBY form via AJAX
    function submitSpbyForm(e) {
      e.preventDefault();
      
      const form = document.getElementById('spbyForm');
      const formData = new FormData(form);
      
      // Convert Rp formatted number to plain number before sending
      const amountField = document.getElementById('amount');
      formData.set('jumlah_pembayaran', parseRp(amountField.value));

      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        const successMsg = document.getElementById('successMessage');
        const errorMsg = document.getElementById('errorMessage');

        if (data.success) {
          // Show success message
          successMsg.textContent = '✓ SPBY berhasil disimpan!';
          successMsg.classList.add('show');
          errorMsg.classList.remove('show');

          // Hide success message after 3 seconds
          setTimeout(() => {
            successMsg.classList.remove('show');
          }, 3000);

          // Notify all open preview windows to reload
          window.postMessage({ type: 'SPBY_DATA_UPDATED' }, '*');
        } else if (data.errors) {
          // Show validation errors
          let errorText = 'Terjadi kesalahan validasi:\n';
          for (let field in data.errors) {
            errorText += '• ' + data.errors[field].join(', ') + '\n';
          }
          errorMsg.textContent = errorText;
          errorMsg.classList.add('show');
          successMsg.classList.remove('show');
        } else {
          errorMsg.textContent = '❌ Gagal menyimpan SPBY: ' + (data.message || 'Terjadi kesalahan');
          errorMsg.classList.add('show');
          successMsg.classList.remove('show');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        const errorMsg = document.getElementById('errorMessage');
        errorMsg.textContent = '❌ Terjadi kesalahan saat menyimpan';
        errorMsg.classList.add('show');
      });
    }

    // Initialize preview on page load
    window.addEventListener('load', updatePreview);
  </script>
</body>
</html>
