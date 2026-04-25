<?php

namespace App\Http\Controllers;

use App\Models\Travel;
use Illuminate\Http\Request;

class KuitansiController extends Controller
{
    public function index()
    {
        $travels = Travel::with(['spby', 'transportItems', 'accommodationItems', 'perdiemItems'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('kuitansi.index', compact('travels'));
    }

    public function show(Travel $travel)
    {
        $travel->load(['spby', 'transportItems', 'accommodationItems', 'perdiemItems']);
        
        return view('kuitansi.show', compact('travel'));
    }

    public function pdf(Travel $travel)
    {
        $travel->load(['spby', 'transportItems', 'accommodationItems', 'perdiemItems']);
        
        return view('kuitansi.form', compact('travel'));
    }

    public function inputData(Travel $travel)
    {
        // The project contains `resources/views/kuitansi/form.blade.php`.
        // Return that view so the input page loads correctly.
        return view('kuitansi.form', compact('travel'));
    }

    public function storeData(Request $request, Travel $travel)
    {
        $validated = $request->validate([
            'pemberi_uang' => 'required|string',
            'tanggal_pembayaran' => 'required|date',
        ]);

        $travel->update($validated);

        // After saving, go back to the kuitansi index so the user sees the updated list
        return redirect()->route('kuitansi.index')
                       ->with('success', 'Data kuitansi berhasil disimpan');
    }
}
