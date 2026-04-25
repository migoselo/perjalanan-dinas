<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Travel;
use App\Models\TransportItem;
use App\Models\AccommodationItem;
use App\Models\PerdiemItem;

class RincianController extends Controller
{
    public function index()
    {
        $travels = Travel::all();
        return view('rincian.index', [
            'travels' => $travels,
        ]);
    }

    public function generateRincianSurat($travelId)
    {
        $travels = Travel::all();
        $selectedTravel = Travel::with(['transportItems', 'accommodationItems', 'perdiemItems'])->find($travelId);

        if (!$selectedTravel) {
            return redirect()->back()->with('error', 'Data perjalanan tidak ditemukan.');
        }

        return view('rincian.index', [
            'travels' => $travels,
            'selectedTravel' => $selectedTravel,
        ]);
    }

    public function indexSurat($travelId = null)
    {
        $travels = Travel::with(['transportItems', 'accommodationItems', 'perdiemItems'])->get();

        if ($travelId) {
            $selectedTravel = Travel::with(['transportItems', 'accommodationItems', 'perdiemItems'])->find($travelId);
            if (!$selectedTravel) {
                return redirect()->route('rincian.index')->with('error', 'Data perjalanan tidak ditemukan.');
            }
        } else {
            // Jika tidak ada travelId, ambil data perjalanan pertama
            $selectedTravel = $travels->first();
        }

        return view('rincian.surat', compact('travels', 'selectedTravel'));
    }


    public function show($travelId)
    {
        $selectedTravel = Travel::with(['transportItems', 'accommodationItems', 'perdiemItems'])->findOrFail($travelId);

        return view('rincian.lihat_surat', compact('selectedTravel'));
    }

    public function signaturePage($travelId = null)
    {
        if ($travelId) {
            $selectedTravel = Travel::with(['transportItems', 'accommodationItems', 'perdiemItems'])->find($travelId);
            if (!$selectedTravel) {
                return redirect()->route('rincian.index')->with('error', 'Data perjalanan tidak ditemukan.');
            }
        } else {
            $travels = Travel::with(['transportItems', 'accommodationItems', 'perdiemItems'])->get();
            $selectedTravel = $travels->first();
        }

        return view('rincian.signature', compact('selectedTravel'));
    }

    public function showForm(Travel $travel)
    {
        $travel = $travel->load(['transportItems', 'accommodationItems', 'perdiemItems']);
        $travels = Travel::with(['transportItems', 'accommodationItems', 'perdiemItems'])->get();
        // Prefill form data from persisted `surat_data` on Travel when available
        $persisted = $travel->surat_data ?? [];
        $formData = [
            'date' => old('tanggal_surat', $persisted['tanggal_surat'] ?? date('Y-m-d')),
            'number' => old('nomor_spby', $persisted['nomor_spby'] ?? ''),
            'purpose' => old('untuk_pembayaran', $persisted['untuk_pembayaran'] ?? ''),
            'amount' => old('jumlah_pembayaran', $persisted['jumlah_pembayaran'] ?? 0),
        ];

        return view('rincian.form', compact('travel', 'travels', 'formData'));
    }

    public function storeForm(Request $request)
    {
        $validated = $request->validate([
            'travel_id' => 'required|exists:travels,id',
            'nip' => 'nullable|digits:18',
            'tanggal_surat' => 'required|date',
            'tanggal_spd' => 'nullable|date',
        ]);

        // Jika nip tidak dikirim, ambil dari relasi Travel->user atau field travel->nip
        if (empty($validated['nip'])) {
            $travel = Travel::with('user')->find($validated['travel_id']);
            $validated['nip'] = optional($travel->user)->nip ?? $travel->nip ?? null;
        }

        // Persist surat data to the Travel record so it's stored permanently
        $travel = Travel::find($validated['travel_id']);
        if ($travel) {
            $travel->surat_data = $validated;
            $travel->save();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data surat berhasil disimpan',
                'redirect' => route('rincian.index')
            ]);
        }

        return redirect()->route('rincian.index')
            ->with('success', 'Data surat berhasil disimpan');
    }
}
