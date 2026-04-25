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
        // Load travels dengan spby dan item-item lainnya
        $travels = Travel::with(['spby', 'transportItems','accommodationItems','perdiemItems'])
                    ->orderByDesc('created_at')
                    ->get();

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

    // Terima data form, simpan ke DB, dan tampilkan preview surat (blade)
    public function preview(Request $request)
    {
        try {
            // Validate required fields
            $request->validate([
                'nomor_spd' => 'required|string',
            ]);
            
            // Debug: Log request data
            \Log::info('SPD Preview Request:', $request->only(['nomor_spd', 'nama_pegawai', 'nip_pegawai', 'travel_id']));
            
            // Jika ada travel_id, update data existing; jika tidak ada, buat baru berdasarkan nomor_spd
            $travel_id = $request->input('travel_id');
            $nomor_spd = $request->input('nomor_spd');
            
            if ($travel_id) {
                // Mode EDIT: Update data yang sudah ada
                $travel = Travel::findOrFail($travel_id);
                \Log::info('Travel Found for Edit:', ['id' => $travel->id]);
            } else {
                // Mode CREATE: Cek apakah sudah ada dengan nomor_spd yang sama
                $travel = Travel::where('nomor_spd', $nomor_spd)->first();
                
                if (!$travel) {
                    $travel = new Travel();
                    $travel->nomor_spd = $nomor_spd;
                    $travel->save();
                    \Log::info('New Travel Created:', ['id' => $travel->id, 'nomor_spd' => $nomor_spd]);
                } else {
                    \Log::info('Travel Found by nomor_spd:', ['id' => $travel->id, 'nomor_spd' => $nomor_spd]);
                }
            }

            // Simpan atau update data SPBY (related to Travel)
            $spby = $travel->spby ?? new \App\Models\SPBY();
            
            // Get all form data
            $spby_data = $request->only([
                'lembar_ke', 'kode_no', 'nomor_spd', 'pejabat_pemberi_perintah',
                'nama_pegawai', 'nip_pegawai', 'pangkat', 'jabatan', 'tingkat_biaya',
                'maksud_perjalanan', 'alat_angkutan', 'tempat_berangkat', 'tempat_tujuan',
                'lama_perjalanan', 'tanggal_berangkat', 'tanggal_kembali',
                'instansi_pembebanan', 'mata_anggaran', 'keterangan',
                'tempat_penerbitan', 'tanggal_penerbitan', 'jabatan_penandatangan',
                'nama_penandatangan', 'nip_penandatangan'
            ]);
            
            \Log::info('SPBY Data to Save:', $spby_data);
            
            $spby->fill($spby_data);

            // Simpan SPBY dengan relationship
            $spby->travel_id = $travel->id;
            $spby->save();
            \Log::info('SPBY Saved:', ['id' => $spby->id, 'travel_id' => $travel->id, 'data' => $spby->toArray()]);

            // Reload travel dengan spby yang baru disimpan
            $travel = Travel::with(['spby', 'user'])->find($travel->id);
            \Log::info('Travel Reloaded:', ['id' => $travel->id, 'spby_loaded' => $travel->spby ? true : false]);


            // Handle pengikut data dari array
            $pengikut = [];
            if ($request->has('pengikut_nama')) {
                $names = $request->input('pengikut_nama', []);
                $tanggals = $request->input('pengikut_tgl_lahir', []);
                $keterangan = $request->input('pengikut_keterangan', []);

                foreach ($names as $index => $name) {
                    if (!empty($name)) {
                        $p = new \stdClass();
                        $p->nama = $name;
                        $p->tanggal_lahir = $tanggals[$index] ?? null;
                        $p->keterangan = $keterangan[$index] ?? null;
                        $pengikut[] = $p;
                    }
                }
            }
            // Persist pengikut into travels.pengikut (JSON)
            if (!empty($pengikut)) {
                $travel->pengikut = $pengikut;
                $travel->save();
                \Log::info('Pengikut saved to travel:', ['travel_id' => $travel->id, 'count' => count($pengikut)]);
            }

            // Redirect to show route with flash success message to avoid duplicate displays
            return redirect()->route('spd.show', $travel->id)->with('success', 'Data SPD berhasil disimpan');
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return back()->withErrors($ve->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('SPD Preview Error:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])->withInput();
        }
    }

    // Tampilkan form input SPD (alias untuk create)
    public function form()
    {
        return view('SPD.form', ['travel' => new Travel()]);
    }

    // Tampilkan form edit SPD
    public function edit(Travel $travel)
    {
        $travel->load(['spby']);
        return view('SPD.form', compact('travel'));
    }

    // Tampilkan SPD dari Travel yang sudah tersimpan
    public function show(Travel $travel)
    {
        // Load relasi yang diperlukan
        $travel->load(['user', 'spby']);
        return view('SPD.show', compact('travel'));
    }

    // Hapus data SPD
    public function destroy(Travel $travel)
    {
        try {
            // Hapus SPBY terlebih dahulu (relasi)
            if ($travel->spby) {
                $travel->spby->delete();
            }

            // Hapus Travel
            $travel->delete();

            return redirect()->route('spd.index')->with('success', 'Data SPD berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error('SPD Delete Error:', ['error' => $e->getMessage()]);
            return redirect()->route('spd.index')->with('error', 'Error menghapus data: ' . $e->getMessage());
        }
    }

    // Generate PDF dari SPD
    public function pdf(Travel $travel)
    {
        try {
            $travel->load(['spby']);
            
            // Generate PDF dengan dompdf
            $pdf = \PDF::loadView('SPD.show', compact('travel'));
            $filename = 'SPD-' . ($travel->spby?->nomor_spd ?? $travel->id) . '.pdf';
            
            // Gunakan stream untuk force download
            return $pdf->stream($filename);
        } catch (\Exception $e) {
            \Log::error('SPD PDF Error:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error membuat PDF: ' . $e->getMessage()], 500);
        }
    }
}