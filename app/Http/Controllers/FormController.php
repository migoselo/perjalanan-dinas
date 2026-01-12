<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Travel;
use Illuminate\Support\Facades\DB;

class FormController extends Controller
{
    public function create()
    {
        return view('form_input.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor_spd' => 'nullable|string|max:255',
            'nomor_surat_tugas' => 'nullable|string|max:255',
            'tanggal_spd' => 'nullable|date',
            'sumber_dana' => 'nullable|string|max:255',
            'kode_mak' => 'nullable|string|max:255',
            'nama_pegawai' => 'nullable|string|max:255',
            'bukti_kas' => 'nullable|string|max:255',
            'uraian_kegiatan' => 'nullable|string',
            'transportations' => 'nullable|array',
            'accommodations' => 'nullable|array',
            'perdiems' => 'nullable|array',
        ]);

        try {
            DB::transaction(function() use ($request, $data) {
                $travel = Travel::create([
                    'nomor_spd' => $data['nomor_spd'] ?? null,
                    'nomor_surat_tugas' => $data['nomor_surat_tugas'] ?? null,
                    'tanggal_spd' => $data['tanggal_spd'] ?? null,
                    'sumber_dana' => $data['sumber_dana'] ?? null,
                    'kode_mak' => $data['kode_mak'] ?? null,
                    'nama_pegawai' => $data['nama_pegawai'] ?? null,
                    'bukti_kas' => $data['bukti_kas'] ?? null,
                    'uraian_kegiatan' => $data['uraian_kegiatan'] ?? null,
                ]);

                foreach ($request->input('transportations', []) as $t) {
                    $travel->transportItems()->create([
                        'mode' => $t['mode'] ?? null,
                        'description' => $t['description'] ?? null,
                        'amount' => $t['amount'] ?? 0,
                    ]);
                }

                foreach ($request->input('accommodations', []) as $h) {
                    $travel->accommodationItems()->create([
                        'name' => $h['name'] ?? null,
                        'nights' => $h['nights'] ?? 0,
                        'price' => $h['price'] ?? 0,
                    ]);
                }

                foreach ($request->input('perdiems', []) as $p) {
                    $travel->perdiemItems()->create([
                        'city' => $p['city'] ?? null,
                        'days' => $p['days'] ?? 0,
                        'amount' => $p['amount'] ?? 0,
                    ]);
                }
            });

            // setelah simpan, redirect ke data.index
            return redirect()->route('data.index')->with('success', 'Data perjalanan berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->withErrors('Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
}