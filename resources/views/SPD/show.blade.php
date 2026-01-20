<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>Surat Perjalanan Dinas</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <style>
    @page { size: A4; margin: 15mm; }
    body {
      font-family: 'Times New Roman', Arial, sans-serif;
      font-size: 11pt;
      color: #000;
      background: #fff;
      margin: 0;
    }
    .container {
      width: 100%;
      max-width: 210mm;
      margin: 0 auto;
      padding: 0;
      box-sizing: border-box;
    }
    .header {
      text-align: center;
      margin-bottom: 10px;
    }
    .header-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
    }
    .header-table td {
      vertical-align: top;
      padding: 4px;
    }
    .header-left {
      text-align: left;
      font-weight: bold;
      text-transform: uppercase;
    }
    .header-right {
      text-align: right;
      font-size: 10pt;
    }
    .title {
      text-align: center;
      font-weight: bold;
      text-transform: uppercase;
      margin-bottom: 5px;
    }
    .title::after {
      content: '';
      display: block;
      width: 100%;
      border-bottom: 1px solid black;
      margin-top: 5px;
    }
    .form-table {
      width: 100%;
      border-collapse: collapse;
    }
    .form-table th, .form-table td {
      border: 1px solid black;
      padding: 6px 8px;
    }
    .form-table .number {
      width: 30px;
      text-align: center;
    }
    .form-table .label {
      width: 250px;
    }
    .form-table .content {
      width: auto;
    }
    .inner-table {
      width: 100%;
      border-collapse: collapse;
    }
    .inner-table th, .inner-table td {
      border: 1px solid black;
      padding: 4px;
      text-align: center;
    }
    .signature {
      margin-top: 20px;
      text-align: right;
    }
    .signature .name {
      margin-top: 50px;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Header -->
    <div class="header">
      <table class="header-table">
        <tr>
          <td class="header-left">
            DIREKTORAT JENDERAL SUMBER DAYA DAN PERANGKAT POS DAN INFORMATIKA<br>
            LOKA MONITOR SPEKTRUM FREKUENSI RADIO TANJUNG SELOR<br>
            KALIMANTAN UTARA
          </td>
          <td class="header-right">
            Lembar Ke : {{ $spd['lembar_ke'] ?? '' }}<br>
            Kode No : {{ $spd['kode_no'] ?? '' }}<br>
            Nomor : {{ $spd['nomor_spd'] ?? '' }}
          </td>
        </tr>
      </table>
    </div>

    <!-- Title -->
    <div class="title">
      SURAT PERJALANAN DINAS (SPD)
    </div>

    <!-- Form Table -->
    <table class="form-table">
      <tr>
        <td class="number">1</td>
        <td class="label">Pejabat berwenang yang memberikan perintah</td>
        <td class="content">Pejabat Pembuat Komitmen Loka Monpsiksal Tanjung Selor</td>
      </tr>
      <tr>
        <td class="number">2</td>
        <td class="label">Nama / NIP Pegawai yang ditugaskan</td>
        <td class="content">{{ $spd['nama'] ?? '' }} / {{ $spd['nip'] ?? '' }}</td>
      </tr>
      <tr>
        <td class="number">3</td>
        <td class="label">
          a. Pangkat / Golongan<br>
          b. Jabatan / Instansi<br>
          c. Tingkat Biaya Perjalanan Dinas
        </td>
        <td class="content">
          a. {{ $spd['pangkat'] ?? '' }}<br>
          b. {{ $spd['jabatan'] ?? '' }}<br>
          c. {{ $spd['tingkat_biaya'] ?? '' }}
        </td>
      </tr>
      <tr>
        <td class="number">4</td>
        <td class="label">Maksud Perjalanan Dinas</td>
        <td class="content">{{ $spd['maksud'] ?? '' }}</td>
      </tr>
      <tr>
        <td class="number">5</td>
        <td class="label">Alat angkutan yang dipergunakan</td>
        <td class="content">{{ $spd['alat_angkutan'] ?? '' }}</td>
      </tr>
      <tr>
        <td class="number">6</td>
        <td class="label">Tempat Berangkat</td>
        <td class="content">{{ $spd['tempat_berangkat'] ?? '' }}</td>
      </tr>
      <tr>
        <td class="number">7</td>
        <td class="label">Tempat Tujuan</td>
        <td class="content">{{ $spd['tempat_tujuan'] ?? '' }}</td>
      </tr>
      <tr>
        <td class="number">8</td>
        <td class="label">Pengikut</td>
        <td class="content">
          <table class="inner-table">
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Tanggal Lahir</th>
              <th>Keterangan</th>
            </tr>
            @foreach ($spd['pengikut'] as $index => $pengikut)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $pengikut['nama'] ?? '' }}</td>
              <td>{{ $pengikut['tanggal_lahir'] ?? '' }}</td>
              <td>{{ $pengikut['keterangan'] ?? '' }}</td>
            </tr>
            @endforeach
          </table>
        </td>
      </tr>
    </table>

    <!-- Signature -->
    <div class="signature">
      <div class="name">{{ $spd['nama_penandatangan'] ?? '' }}</div>
      <div class="position">{{ $spd['jabatan_penandatangan'] ?? '' }}</div>
    </div>
  </div>
</body>
</html>