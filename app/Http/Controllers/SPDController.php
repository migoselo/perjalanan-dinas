<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\Travel;

class SPDController extends Controller
{
    // Tampilkan daftar SPD
    public function index()
    {
        $travels = Travel::with(['transportItems','accommodationItems','perdiemItems'])
                    ->orderByDesc('created_at')->get();

        return view('SPD.index', compact('travels'));
    }

    // Tampilkan form input / upload CSV
    public function create()
    {
        return view('SPD.form');
    }

    // Parse CSV upload dan kembalikan JSON untuk prefilling form (AJAX)
    public function parseCsv(Request $request)
    {
        if (! $request->hasFile('csv')) {
            return response()->json(['error' => 'File CSV tidak ditemukan'], 400);
        }

        $file = $request->file('csv');

        $handle = fopen($file->getRealPath(), 'r');
        if (! $handle) {
            return response()->json(['error' => 'Gagal membuka file'], 500);
        }

        $rows = [];
        while (($row = fgetcsv($handle, 0, ",")) !== false) {
            // gabungkan kolom jadi satu cell string untuk mempermudah pencarian
            $rows[] = array_map(function ($c) { return trim($c); }, $row);
        }
        fclose($handle);

        // Gabungkan semua sel jadi satu teks besar (untuk pencarian cepat)
        $flat = implode("\n", array_map(function($r){
            return implode(' | ', $r);
        }, $rows));

        // Sederhanakan parsing berdasarkan pola umum di CSV sample
        $data = [];

        // 1) Cari nama + NIP => cari cell yang mengandung 'NIP.' atau 'NIP'
        foreach ($rows as $r) {
            foreach ($r as $cell) {
                if (!$cell) continue;
                // cek pattern "NAME\nNIP. 1234..." atau "NAME NIP. 123..."
                if (preg_match('/^(.*?)(?:\s*[\r\n]+|\s+)NIP\.?\s*[:.]?\s*(\d{9,20})/i', $cell, $m)) {
                    $data['recipient_name'] = trim($m[1]);
                    $data['recipient_nip'] = trim($m[2]);
                    break 2;
                }
                // atau cell sendiri adalah NIP panjang (kadang ada kolom terpisah)
                if (preg_match('/^\d{9,20}$/', $cell)) {
                    // coba nama di sebelah kiri cell (previous columns)
                    // Not very strict but will try to find earlier non-empty cell in same row
                    // find row index
                    // handled below in fallback
                }
            }
        }

        // Fallback: cari baris yang mengandung "Nama / NIP" lalu ambil cell setelahnya
        foreach ($rows as $r) {
            $line = implode(' ', $r);
            if (stripos($line, 'Nama / NIP') !== false || stripos($line, 'Nama/NIP') !== false) {
                // cari cell yang mengandung NIP di baris ini
                foreach ($r as $cell) {
                    if (preg_match('/^(.*?)(?:\s*[\r\n]+|\s+)NIP\.?\s*[:.]?\s*(\d{9,20})/i', $cell, $m)) {
                        $data['recipient_name'] = trim($m[1]);
                        $data['recipient_nip'] = trim($m[2]);
                        break 2;
                    }
                    if (preg_match('/\d{9,20}/', $cell, $m2)) {
                        // take previous non-empty cell as name
                        $idx = array_search($cell, $r);
                        for ($i = $idx-1; $i >=0; $i--) {
                            if (trim($r[$i]) !== '') {
                                $data['recipient_name'] = trim($r[$i]);
                                $data['recipient_nip'] = trim($m2[0]);
                                break 2;
                            }
                        }
                    }
                }
            }
        }

        // 2) Cari tanggal terbit / tanggal dikeluarkan -> cari "Tanggal" di flat
        if (preg_match('/Tanggal\s*[:\s]*[,]*\s*([0-9]{1,2}\s+\w+\s+[0-9]{4}|[A-Za-z]+\s+[0-9]{4})/i', $flat, $m)) {
            $data['date'] = trim($m[1]);
        } else {
            // cari kata "Tanggal" lalu ambil token berikutnya di text
            if (preg_match('/Tanggal\s*[:,]?\s*([^\n,]+)/i', $flat, $m2)) {
                $data['date'] = trim($m2[1]);
            }
        }

        // 3) Cari tanggal berangkat / kembali (format dd Month yyyy)
        preg_match_all('/\b([0-9]{1,2}\s+(?:Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\s+[0-9]{4})\b/iu', $flat, $dates);
        if (! empty($dates[1])) {
            // assign first as departure, last as return (heuristic)
            $data['departure_date'] = $dates[1][0];
            $data['return_date'] = end($dates[1]);
        }

        // 4) Cari tujuan & tempat berangkat (kata 'Tempat Berangkat' / 'Tempat Tujuan')
        foreach ($rows as $r) {
            $line = implode(' ', $r);
            if (stripos($line, 'Tempat Berangkat') !== false) {
                // ambil token setelahnya
                if (preg_match('/Tempat Berangkat[^\w]*\s*([A-Z0-9 \-]+)/i', $line, $m)) {
                    $data['from'] = trim($m[1]);
                } else {
                    // ambil next non-empty cell
                    $idx = array_search('Tempat Berangkat', $r);
                }
            }
            if (stripos($line, 'Tempat Tujuan') !== false) {
                if (preg_match('/Tempat Tujuan[^\w]*\s*([A-Z0-9 \-]+)/i', $line, $m2)) {
                    $data['to'] = trim($m2[1]);
                }
            }
        }
        // fallback: langsung cari kata BANDUNG, TANJUNG SELOR dalam flat
        if (empty($data['from']) && preg_match('/TANJUNG SELOR/i', $flat)) $data['from'] = 'TANJUNG SELOR';
        if (empty($data['to']) && preg_match('/BANDUNG/i', $flat)) $data['to'] = 'BANDUNG';

        // 5) Kegiatan / purpose -> cari baris yang mengandung 'Maksud Perjalanan Dinas' atau 'Maksud'
        if (preg_match('/Maksud Perjalanan Dinas[^\n]*\n?(.{5,200})/i', $flat, $m)) {
            $data['purpose'] = trim($m[1]);
        } else {
            // cari kalimat 'Menghadiri ...'
            if (preg_match('/(Menghadiri[^\n,]+)/i', $flat, $m2)) {
                $data['purpose'] = trim($m2[1]);
            }
        }

        // 6) Activity MAK / code -> cari pattern 7437...
        if (preg_match('/(7\d{3}\.[A-Z]{3}\.\d{3}\.\d{3}\.[A-Z]\.\d{6}|\d{3,}\.[A-Z0-9\.\-]+)/i', $flat, $m)) {
            $data['activity_mak'] = trim($m[0]);
        } else {
            if (preg_match('/7437\.[\w\.\-]+/i', $flat, $m2)) {
                $data['activity_mak'] = trim($m2[0]);
            }
        }

        // 7) Nomor SPD
        if (preg_match('/Nomor\s*[:,]?\s*([^\n,]+)/i', $flat, $m)) {
            $data['number'] = trim($m[1]);
        } elseif (preg_match('/Nomor[:,]?\s*([0-9\/A-Z\-\s]+)/i', $flat, $m2)) {
            $data['number'] = trim($m2[1]);
        }

        // 8) Default nilai jika masih kosong
        $defaults = [
            'recipient_name' => $data['recipient_name'] ?? '',
            'recipient_nip' => $data['recipient_nip'] ?? '',
            'date' => $data['date'] ?? ($data['departure_date'] ?? ''),
            'departure_date' => $data['departure_date'] ?? '',
            'return_date' => $data['return_date'] ?? '',
            'from' => $data['from'] ?? '',
            'to' => $data['to'] ?? '',
            'purpose' => $data['purpose'] ?? '',
            'activity_mak' => $data['activity_mak'] ?? '',
            'number' => $data['number'] ?? '',
        ];

        return response()->json(['ok' => true, 'data' => $defaults]);
    }

    // Terima data form dan tampilkan preview surat (blade)
    public function preview(Request $request)
    {
        $data = $request->only([
            'recipient_name','recipient_nip','date','departure_date','return_date',
            'from','to','purpose','activity_mak','number','code','amount','amount_in_words'
        ]);

        // fallback format: jika date kosong gunakan departure_date
        if (empty($data['date']) && !empty($data['departure_date'])) {
            $data['date'] = $data['departure_date'];
        }

        return view('SPD.show', ['spd' => $data]);
    }

    // Tampilkan SPD dari Travel yang sudah tersimpan
    public function show(Travel $travel)
    {
        // Ambil data SPD dari travel extra column (JSON)
        $spd = $travel->extra ? json_decode($travel->extra, true) : [];
        
        // Fallback: compile dari travel attributes
        if (empty($spd)) {
            $spd = [
                'recipient_name' => $travel->nama_pegawai ?? '',
                'date' => $travel->tanggal_spd ?? '',
                'number' => $travel->nomor_spd ?? '',
                'activity_mak' => $travel->kode_mak ?? '',
                'purpose' => $travel->uraian_kegiatan ?? '',
            ];
        }
        
        return view('SPD.show', ['spd' => $spd]);
    }
}