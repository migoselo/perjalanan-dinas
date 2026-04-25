<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rincian Biaya Perjalanan Dinas</title>
        <style>
    /* RESET */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* SETTING HALAMAN A4 */
    @page {
        size: A4;
        margin: 15mm 15mm 15mm 15mm;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #333333;
        padding: 20px;
        font-size: 7pt;
    }

    /* CONTAINER PAS A4 */
    .container {
        width: 190mm;              /* lebar efektif A4 */
        min-height: 277mm;         /* tinggi efektif A4 */
        margin: auto;
        background-color: white;
        padding: 10mm;
        box-shadow: none;          /* supaya bersih saat print */
        border-radius: 0;
    }

    /* HEADER */
    .header {
        display: flex;
        align-items: center;
        margin-bottom: 3px;
        gap: -15px; /* spasi antara logo dan teks */
    }

    .logo {
        width: 70px;
        height: 70px;
        margin-right: 5px; /* spasi kanan logo */
    }

    .logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .header-text h3 {
        font-size: 7pt;
        font-weight: bold;
        line-height: 1.35; /* beris lebih rapat */
        margin: 0; /* pastikan tidak ada jarak default */
    }

    /* JUDUL */
    .title {
        text-align: center;
        font-weight: bold;
        font-size: 7pt;
        margin: 6px 0;
    }

    /* INFO DOKUMEN */
    .document-info {
        margin-top: 25px;
        margin-bottom: 15px;
        font-size: 7pt;
    }

    .document-info div {
        display: flex;
        margin-bottom: 2px;
    }

    .document-info label {
        width: 130px;
    }

    .document-info span {
        margin: 0 8px;
    }

    /* TABEL */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 3px;
        font-size: 7pt;
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
    }

    th, td {
        border-left: 1px solid #000;
        border-right: 1px solid #000;
    }

    th {
        background-color: #d3d3d3;
        padding: 6px;
        font-weight: bold;
        text-align: center;
        border-bottom: 1px solid #000;
    }

    td {
        padding: 6px;
        border-bottom: none;
    }

    /* Garis horizontal di bawah judul kategori (TRANSPORTASI, PENGINAPAN, UANG HARIAN) */
    tr:has(td:first-child:contains("I.")) td,
    tr:has(td:first-child:contains("II.")) td,
    tr:has(td:first-child:contains("III.")) td {
        border-bottom: 1px solid #000;
    }

    /* KOLOM */
    .no-column {
        width: 45px;
        text-align: center;
        font-weight: bold;
    }

    .uraian-column {
        width: 360px;
    }

    .jumlah-column {
        width: 110px;
        text-align: right;
    }

    .keterangan-column {
        text-align: center;
        width: 200px;
    }

    .sub-item {
        padding-left: 15px;
    }

    .total-row {
        font-weight: bold;
        background-color: white;
    }

    .total-row td {
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
    }

    /* TERBILANG */
    .terbilang-cell {
        position: relative;
        padding: 10px;
        font-size: 7pt;
        font-weight: bold;
        text-align: center;
    }

    .terbilang-label {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        white-space: nowrap;
    }

    .terbilang-text {
        display: block;
        text-align: center;
    }

    /* HALAMAN TANDA TANGAN */
    @media print {
        .container {
            page-break-after: always;
        }

        body {
            background: white;
        }
    }
    </style>

</head>
<body>
    @if ($selectedTravel)
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
            </div>
            <div class="header-text">
                <h3>KEMENTERIAN KOMUNIKASI DAN INFORMATIKA</h3>
                <h3>DIREKTORAT JENDERAL SUMBER DAYA DAN PERANGKAT POS DAN INFORMATIKA</h3>
                <h3>LOKA MONITOR SPEKTRUM FREKUENSI RADIO TANJUNG SELOR</h3>
            </div>
        </div>
        
        <div class="title">
            RINCIAN BIAYA PERJALANAN DINAS
        </div>
        
        <div class="document-info">
            <div>
                <label>Lampiran SPD No.</label>
                <span>:</span>
                <strong>{{ $selectedTravel->nomor_spd ?? '-' }}</strong>
            </div>
            <div>
                <label>Tanggal</label>
                <span>:</span>
                <strong>
                @php
                    // Prefer persisted `surat_data` on the Travel model (saved by controller).
                    $suratData = $selectedTravel->surat_data ?? null;
                    $tanggalSpd = null;
                    if ($suratData && isset($suratData['tanggal_spd']) && !empty($suratData['tanggal_spd'])) {
                        try {
                            $tanggalSpd = \Carbon\Carbon::createFromFormat('Y-m-d', $suratData['tanggal_spd'])->format('d F Y');
                        } catch (\Exception $e) {
                            $tanggalSpd = null;
                        }
                    }
                @endphp

                @if($tanggalSpd)
                    {{ $tanggalSpd }}
                @else
                    {{ optional($selectedTravel->tanggal_spd)->format('d F Y') ?? '-' }}
                @endif
                </strong>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>  
                    <th rowspan="2" class="no-column">NO</th>
                    <th rowspan="2" class="uraian-column">URAIAN</th>
                    <th colspan="2">PERINCIAN BIAYA</th>
                </tr>
                <tr>
                    <th class="jumlah-column">JUMLAH</th>
                    <th class="keterangan-column">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                <!-- TRANSPORTASI -->
                <tr>
                    <td class="no-column">I.</td>
                    <td><strong>TRANSPORTASI :</strong></td>
                    <td class="jumlah-column">Rp. {{ number_format($selectedTravel->transport_total, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
                @if ($selectedTravel->transportItems->count() > 0)
                    @foreach ($selectedTravel->transportItems as $item)
                    <tr>
                        <td></td>
                        <td class="sub-item">{{ $item->mode }}</td>
                        <td class="jumlah-column">Rp. {{ number_format($item->amount, 0, ',', '.') }}</td>
                        <td class="keterangan-column">{{ $item->description }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="text-align: center;">Tidak ada data transportasi</td>
                    </tr>
                @endif

                <!-- PENGINAPAN -->
                <tr>
                    <td class="no-column">II.</td>
                    <td><strong>PENGINAPAN :</strong></td>
                    <td class="jumlah-column">Rp. {{ number_format($selectedTravel->accommodation_total, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
                @if ($selectedTravel->accommodationItems->count() > 0)
                    @foreach ($selectedTravel->accommodationItems as $item)
                    <tr>
                        <td></td>
                        <td class="sub-item">{{ $item->name }}</td>
                        <td class="jumlah-column">Rp. {{ number_format($item->nights * $item->price, 0, ',', '.') }}</td>
                        <td class="keterangan-column">{{ $item->nights }} Hari X Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="text-align: center;">Tidak ada data penginapan</td>
                    </tr>
                @endif

                <!-- UANG HARIAN -->
                <tr>
                    <td class="no-column">III.</td>
                    <td><strong>UANG HARIAN :</strong></td>
                    <td class="jumlah-column">Rp. {{ number_format($selectedTravel->perdiem_total, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
                @if ($selectedTravel->perdiemItems->count() > 0)
                    @foreach ($selectedTravel->perdiemItems as $item)
                    <tr>
                        <td></td>
                        <td class="sub-item">{{ $item->city }}</td>
                        <td class="jumlah-column">Rp. {{ number_format($item->days * $item->amount, 0, ',', '.') }}</td>
                        <td class="keterangan-column">{{ $item->days }} Hari X Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="text-align: center;">Tidak ada data uang harian</td>
                    </tr>
                @endif

                <!-- TOTAL -->
                <tr class="total-row">
                    <td colspan="2" style="text-align: center;"><strong>JUMLAH &nbsp;&nbsp;&nbsp; :</strong></td>
                    <td class="jumlah-column"><strong>Rp. {{ number_format($selectedTravel->grand_total, 0, ',', '.') }}</strong></td>
                    <td></td>
                </tr>
 
                <tr>
                    <td colspan="4" class="terbilang-cell">
                        <span class="terbilang-label"><strong>Terbilang :</strong></span>
                        <span class="terbilang-text">
                            {{ trim(terbilang($selectedTravel->grand_total)) }} Rupiah
                        </span>
                    </td>
                </tr>


            </tbody>
        </table>

        <!-- HALAMAN TANDA TANGAN -->
        <div style="margin-top: 20px; page-break-before: always; padding-top: 0px;">
            <div class="signature-section" style="font-size: 7pt;">
                <div style="text-align: right; margin-bottom: 20px;">
                    <strong>Tanjung Selor,</strong> &nbsp;&nbsp;&nbsp; <strong>
                    @php
                        // Prefer persisted surat_data saved on the Travel model
                        $suratData = $selectedTravel->surat_data ?? null;
                        $tanggalSurat = null;

                        if ($suratData && isset($suratData['tanggal_surat']) && !empty($suratData['tanggal_surat'])) {
                            try {
                                $tanggalSurat = \Carbon\Carbon::createFromFormat('Y-m-d', $suratData['tanggal_surat'])->format('d F Y');
                            } catch (\Exception $e) {
                                $tanggalSurat = null;
                            }
                        }

                        if (!$tanggalSurat) {
                            $tanggalSurat = optional($selectedTravel->tanggal_spd)->format('d F Y') ?? 'Desember 2025';
                        }

                        echo $tanggalSurat;
                    @endphp
                    </strong>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                    <div style="width: 48%;">
                        <p style="margin: 3px 0;">Telah dibayar sejumlah uang</p>
                        <p style="margin: 3px 0;"><strong>Rp {{ number_format($selectedTravel->grand_total, 0, ',', '.') }}</strong></p>
                        <br>
                        <p style="margin: 3px 0;"><strong>Bendahara Pengeluaran,</strong></p>
                        <br><br><br><br>
                        <p style="margin: 3px 0;"><strong><u>ISWANTONO</u></strong></p>
                        <p style="margin: 3px 0;"><strong>NIP. 197305062006041004</strong></p>
                    </div>
                    
                    <div style="width: 48%; text-align: right; margin-left: auto;">
                        <p style="margin: 3px 0;">Telah menerima sejumlah uang</p>
                        <p style="margin: 3px 0;"><strong>Rp {{ number_format($selectedTravel->grand_total, 0, ',', '.') }}</strong></p>
                        <br>
                        <p style="margin: 3px 0;"><strong>Yang berpergian,</strong></p>
                        <br><br><br><br>
                        <p style="margin: 3px 0;"><strong><u>{{ strtoupper($selectedTravel->nama_pegawai ?? 'NAMA') }}</u></strong></p>
                        @php $suratData = $selectedTravel->surat_data ?? null; @endphp
                        <p style="margin: 3px 0;"><strong>NIP. {{ optional($selectedTravel->user)->nip ?? $selectedTravel->nip ?? ($suratData['nip'] ?? null) ?? '-' }}</strong></p>
                    </div>
                </div>
                
                <hr style="border: 1px solid #000; margin: 20px 0;">
                
                <div style="margin-top: 5px; margin-bottom: 20px;">
                    <table style="width: 100%; border: none; border-collapse: collapse;">
                        <tr>
                            <td style="width: 350px; padding: 5px 0; border: none;">Ditetapkan sejumlah</td>
                            <td style="width: 30px; padding: 5px 0; border: none;"><strong>:Rp.</strong></td>
                            <td style="width: 100px; text-align: right; padding-right: 10px; padding: 5px 0; border: none;"><strong>{{ number_format($selectedTravel->grand_total, 0, ',', '.') }}</strong></td>
                            <td style="padding: 5px 0; border: none;"></td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 0; border: none;">Uang Muka Kerja yang telah dibayar semula</td>
                            <td style="padding: 5px 0; border: none;"><strong>:Rp.</strong></td>
                            <td style="text-align: right; padding-right: 10px; padding: 5px 0; border: none;"><strong>-</strong></td>
                            <td style="padding: 5px 0; border: none;"></td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 0; border: none;">Sisa, kurang / lebih</td>
                            <td style="padding: 5px 0; border: none;"><strong>:Rp.</strong></td>
                            <td style="text-align: right; padding-right: 10px; padding: 5px 0; border: none;"><strong>{{ number_format($selectedTravel->grand_total, 0, ',', '.') }}</strong></td>
                            <td style="padding: 5px 0; border: none;"></td>
                        </tr>
                    </table>
                </div>
                
                <div style="margin-top: 20px;">
                    <p style="text-align: right; margin-top: 40px; margin: 3px 0;"><strong>Pejabat Pembuat Komitmen</strong></p>
                    <p style="text-align: right; margin: 80px 0 6px 0;"><strong><u>DENI WIJAYANTO</u></strong></p>
                    <p style="text-align: right; margin: 6px 0 3px 0;"><strong>NIP. 198005072006041005</strong></p>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="container" style="text-align: center; padding: 40px;">
        <p style="color: #999; font-size: 14px;">Silakan pilih data perjalanan dari dropdown di atas</p>
    </div>
    @endif
</body>
</html>