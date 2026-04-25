<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Perintah Dinas</title>
    <style>
        @page {
            size: 210mm 297mm;
            margin: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: white;
            width: 210mm;
            height: 297mm;
            padding: 10mm 15mm;
            position: relative;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            border: 1px solid #ddd;
            font-size: 6pt;
        }
        
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            padding-bottom: 5px;
        }
        
        .logo {
            width: 65px;
            height: 65px;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .header-text {
            flex: 1;
        }
        
        .header-title {
            font-size: 6pt;
            font-weight: bold;
            line-height: 1.3;
            margin-bottom: 2px;
            position: relative;
            left: -23px;
        }
        
        .header-info {
            position: relative;
            left: -100px;
            text-align: left;
            font-size: 6pt;
        }
        
        .header-info table {
            border: none;
            font-size: 6pt;
            width: auto;
        }
        
        .header-info td {
            border: none;
            padding: 1px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 6pt;
        }
        
        td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        
        .no-col {
            width: 30px;
            text-align: center;
            font-weight: normal;
        }
        
        .label-col {
            width: 45%;
            font-weight: normal;
        }
        
        .content-col {
            font-weight: normal;
        }
        
        .bold {
            font-weight: bold;
        }
        
        @media print {
            html {
                background: white;
                padding: 0;
            }
            
            body {
                margin: 0;
                padding: 15mm 15mm;
                box-shadow: none;
                border: none;
                width: 100%;
                height: auto;
            }
            
            .action-toolbar {
                display: none !important;
            }
        }
        
        .action-toolbar {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.95);
            padding: 12px 20px;
            border-radius: 50px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            display: flex;
            gap: 8px;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }
        
        .action-toolbar button,
        .action-toolbar a {
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            font-size: 13px;
            transition: all 0.3s ease;
        }
        
        .action-toolbar button:hover,
        .action-toolbar a:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        
        .action-toolbar .btn-print {
            background: #0047AB;
            color: white;
        }
        
        .action-toolbar .btn-back {
            background: #6c757d;
            color: white;
        }
        
        .action-toolbar .btn-edit {
            background: #ffc107;
            color: black;
        }
        
        .action-toolbar .btn-download {
            background: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <div class="action-toolbar">
        <button class="btn-print" onclick="downloadPDF()">🖨️ Print</button>
        <a href="{{ route('spd.index') }}" class="btn-back">← Kembali</a>
        @if($travel->id)
        <a href="{{ route('spd.edit', $travel->id) }}" class="btn-edit">✏️ Edit</a>
        @endif
    </div>
        @if(session('success'))
            <div id="flash-success-spd-show" style="max-width:1000px;margin:10px auto;padding:10px;background:#d4edda;color:#155724;border:1px solid #c3e6cb;border-radius:4px;text-align:center;">{{ session('success') }}</div>
        @endif
    
    <div style="margin-top: 0;">
    <div class="header">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </div>
        <div class="header-text">
            <div class="header-title">
                DIREKTORAT JENDERAL SUMBER DAYA DAN PERANGKAT POS DAN INFORMATIKA<br>
                LOKA MONITOR SPEKTRUM FREKUENSI RADIO TANJUNG SELOR<br>
                KALIMANTAN UTARA
            </div>
        </div>
        <div class="header-info">
            <table>
                <tr>
                    <td style="text-align: left;">Lembar Ke</td>
                    <td style="padding: 1px 5px;">:</td>
                    <td>{{ $travel->spby?->lembar_ke ?? '' }}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">Kode No.</td>
                    <td style="padding: 1px 5px;">:</td>
                    <td>{{ $travel->spby?->kode_no ?? '' }}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">Nomor</td>
                    <td style="padding: 1px 5px;">:</td>
                    <td>{{ $travel->nomor_spd ?? '' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <table>
        <tr>
            <td class="no-col">1</td>
            <td class="label-col">Pejabat berwenang yang memberi perintah</td>
            <td class="content-col bold">{{ $travel->spby?->pejabat_pemberi_perintah ?? 'Pejabat Pembuat Komitmen Loka MonSpeKFreRad Tanjung Selor' }}</td>
        </tr>
        <tr>
            <td class="no-col">2</td>
            <td class="label-col">Nama / NIP Pegawai yang diperintah</td>
            <td class="content-col bold">{{ $travel->nama_pegawai ?? '' }}<span style="margin-left: 200px;">{{ $travel->nip ?? '' }}</span></td>
        </tr>
        <tr>
            <td class="no-col">3</td>
            <td class="label-col">
                a. &nbsp;&nbsp;Pangkat / Golongan Ruang Gaji<br><br>
                b. &nbsp;&nbsp;Jabatan / Instansi<br><br>
                c. &nbsp;&nbsp;Tingkat Biaya Perjalanan Dinas
            </td>
            <td class="content-col">
                a. &nbsp;&nbsp;<span class="bold">{{ $travel->spby?->pangkat ?? '' }}</span><br><br>
                b. &nbsp;&nbsp;<span class="bold">{{ $travel->spby?->jabatan ?? '' }}</span><br><br>
                c. &nbsp;&nbsp;<span class="bold">{{ $travel->spby?->tingkat_biaya ?? '' }}</span>
            </td>
        </tr>
        <tr>
            <td class="no-col" rowspan="2">4</td>
            <td class="label-col" rowspan="2">Maksud Perjalanan Dinas</td>
            <td class="content-col bold" style="border-bottom: none;">{{ $travel->spby?->maksud_perjalanan ?? '' }}</td>
        </tr>
        <tr>
            <td class="content-col" style="height: 100px; border-top: none;"></td>
        </tr>
        <tr>
            <td class="no-col">5</td>
            <td class="label-col">Alat angkutan yang dipergunakan</td>
            <td class="content-col bold">{{ $travel->spby?->alat_angkutan ?? '' }}</td>
        </tr>
        <tr>
            <td class="no-col">6</td>
            <td class="label-col">
                a. &nbsp;&nbsp;Tempat Berangkat<br><br>
                b. &nbsp;&nbsp;Tempat Tujuan
            </td>
            <td class="content-col">
                <span class="bold">{{ $travel->spby?->tempat_berangkat ?? '' }}</span><br><br>
                <span class="bold">{{ $travel->spby?->tempat_tujuan ?? '' }}</span>
            </td>
        </tr>
        <tr>
            <td class="no-col">7</td>
            <td class="label-col">
                a. &nbsp;&nbsp;Lamanya Perjalanan Dinas<br><br>
                b. &nbsp;&nbsp;Tanggal Berangkat<br><br>
                c. &nbsp;&nbsp;Tanggal Harus Kembali
            </td>
            <td class="content-col">
                a. &nbsp;&nbsp;<span class="bold">{{ $travel->spby?->lama_perjalanan ?? '' }} &nbsp;(Hari)</span><br><br>
                b. &nbsp;&nbsp;<span class="bold">{{ $travel->spby?->tanggal_berangkat ? \Carbon\Carbon::parse($travel->spby?->tanggal_berangkat)->format('d F Y') : '' }}</span><br><br>
                c. &nbsp;&nbsp;<span class="bold">{{ $travel->spby?->tanggal_kembali ? \Carbon\Carbon::parse($travel->spby?->tanggal_kembali)->format('d F Y') : '' }}</span>
            </td>
        </tr>
        <tr>
            <td class="no-col">8</td>
            <td colspan="2" style="padding: 0;">
                <table style="width: 100%; border: none; margin: 0;">
                    <tr>
                        <td style="border: none; border-right: 1px solid #000; width: 33%; padding: 6px 8px;">Pengikut <span style="margin-left: 80px;">Nama</span></td>
                        <td style="border: none; border-right: 1px solid #000; width: 33%; text-align: center; padding: 6px 8px;"><span class="bold">Tanggal Lahir</span></td>
                        <td style="border: none; width: 34%; text-align: center; padding: 6px 8px;"><span class="bold">Keterangan</span></td>
                    </tr>
                    @forelse($travel->pengikut ?? [] as $index => $pengikut)
                    <tr>
                        <td style="border: none; border-right: 1px solid #000; border-top: 1px solid #000; padding: 6px 8px;">{{ $index + 1 }}.</td>
                        <td style="border: none; border-right: 1px solid #000; border-top: 1px solid #000; padding: 6px 8px;">{{ $pengikut->nama ?? '' }}</td>
                        <td style="border: none; border-top: 1px solid #000; padding: 6px 8px;">{{ $pengikut->tanggal_lahir ? \Carbon\Carbon::parse($pengikut->tanggal_lahir)->format('d-m-Y') : '' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td style="border: none; border-right: 1px solid #000; border-top: 1px solid #000; padding: 6px 8px;">1.</td>
                        <td style="border: none; border-right: 1px solid #000; border-top: 1px solid #000; padding: 6px 8px;"></td>
                        <td style="border: none; border-top: 1px solid #000; padding: 6px 8px;"></td>
                    </tr>
                    <tr>
                        <td style="border: none; border-right: 1px solid #000; padding: 6px 8px;">2.</td>
                        <td style="border: none; border-right: 1px solid #000; padding: 6px 8px;"></td>
                        <td style="border: none; padding: 6px 8px;"></td>
                    </tr>
                    <tr>
                        <td style="border: none; border-right: 1px solid #000; padding: 6px 8px;">3.</td>
                        <td style="border: none; border-right: 1px solid #000; padding: 6px 8px;"></td>
                        <td style="border: none; padding: 6px 8px;"></td>
                    </tr>
                    <tr>
                        <td style="border: none; border-right: 1px solid #000; padding: 6px 8px;">4.</td>
                        <td style="border: none; border-right: 1px solid #000; padding: 6px 8px;"></td>
                        <td style="border: none; padding: 6px 8px;"></td>
                    </tr>
                    <tr>
                        <td style="border: none; border-right: 1px solid #000; padding: 6px 8px;">5.</td>
                        <td style="border: none; border-right: 1px solid #000; padding: 6px 8px;"></td>
                        <td style="border: none; padding: 6px 8px;"></td>
                    </tr>
                    @endforelse
                </table>
            </td>
        </tr>
        <tr>
            <td class="no-col">9</td>
            <td class="label-col">
                Pembebanan Anggaran<br><br>
                a. &nbsp;&nbsp;Instansi<br><br>
                b. &nbsp;&nbsp;Mata Anggaran
            </td>
            <td class="content-col">
                <br><br>
                a. &nbsp;&nbsp;<span class="bold">{{ $travel->spby?->instansi_pembebanan ?? 'Loka Monitor Spektrum Frekuensi Radio Tanjung Selor' }}</span><br><br>
                b. &nbsp;&nbsp;<span class="bold">{{ $travel->kode_mak ?? $travel->spby?->mata_anggaran ?? '' }}</span>
            </td>
        </tr>
        <tr>
            <td class="no-col">10</td>
            <td class="label-col">Keterangan lain-lain</td>
            <td class="content-col">{{ '' }}</td>
        </tr>
    </table>

    <div style="margin-top: 20px; display: flex; justify-content: space-between;">
        <div style="width: 28%;"></div>
        <div style="width: 28%;">
            <div style="font-size: 6pt; margin-bottom: 3px;">
                <table style="border: none; width: 100%;">
                    <tr>
                        <td style="border: none; padding: 2px 0; width: 100px;">Dikeluarkan di</td>
                        <td style="border: none; padding: 2px 0; width: 10px;">:</td>
                        <td style="border: none; padding: 2px 0; font-weight: bold;">{{ $travel->spby?->tempat_penerbitan ?? 'Tanjung Selor' }}</td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 2px 0; border-bottom: 1px solid #000;">Tanggal</td>
                        <td style="border: none; padding: 2px 0; border-bottom: 1px solid #000;">:</td>
                        <td style="border: none; padding: 2px 0; border-bottom: 1px solid #000; font-weight: bold;">{{ $travel->spby?->tanggal_penerbitan ? \Carbon\Carbon::parse($travel->spby?->tanggal_penerbitan)->format('d F Y') : '' }}</td>
                    </tr>
                </table>
            </div>
            <div style="font-size: 6pt; margin-top: 15px; line-height: 1.4;">
                PEJABAT PEMBUAT KOMITMEN<br>
                LOKA MONITOR SPEKTRUM FREKUENSI RADIO<br>
                TANJUNG SELOR
            </div>
            <div style="height: 60px;"></div>
            <div style="font-size: 6pt;">
                <div style="font-weight: bold; text-decoration: underline;">DENI WIJAYANTO, S.T.</div>
                <div style="margin-top: 2px;">NIP. 198005072006041005</div>
            </div>
        </div>
    </div>
    </div>
</body>
</html>

<script>
function downloadPDF() {
    // Simpan CSS media print saat ini
    let originalTitle = document.title;
    
    // Set title untuk PDF
    document.title = 'SPD-{{ $travel->spby?->nomor_spd ?? $travel->id }}';
    
    // Print dialog
    window.print();
    
    // Restore title
    document.title = originalTitle;
}
</script>
<script>
// Auto-hide flash success message after 5 seconds
setTimeout(function(){
    var el = document.getElementById('flash-success-spd-show');
    if(el){ el.style.transition = 'opacity 0.5s'; el.style.opacity = 0; setTimeout(function(){ el.remove(); }, 500); }
}, 5000);
</script>
