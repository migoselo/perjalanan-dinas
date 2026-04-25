<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Travel;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    public function index(Request $request)
    {
        $travels = Travel::with(['transportItems','accommodationItems','perdiemItems'])
                    ->orderByDesc('created_at')
                    ->paginate(12);

        $selected = null;
        if ($request->has('selected')) {
            $selected = Travel::with(['transportItems','accommodationItems','perdiemItems'])
                        ->find($request->query('selected'));
        }

        if (!$selected && $travels->count()) {
            $selected = $travels->first();
        }

        return view('Data.index', compact('travels','selected'));
    }

    // return full page (optional)
    public function show(Travel $travel)
    {
        $travel->load(['transportItems','accommodationItems','perdiemItems']);
        return view('Data.show', compact('travel'));
    }

    // return partial HTML untuk AJAX (panel kanan)
    public function partial(Travel $travel)
    {
        $travel->load(['transportItems','accommodationItems','perdiemItems']);
        return view('Data._detail', compact('travel'));
    }

    // hapus data
    public function destroy(Travel $travel)
    {
        $travel->delete();
        return redirect()->route('data.index')->with('success','Data dihapus.');
    }

    public function edit(Travel $travel)
    {
        $travel->load(['transportItems', 'accommodationItems', 'perdiemItems']);
        return view('Data.edit', compact('travel'));
    }

    public function update(Request $request, Travel $travel)
    {
        $validatedData = $request->validate([
            'spd_number' => 'required|string|max:255',
            'date' => 'required|date',
            'transportItems.*.description' => 'nullable|string',
            'transportItems.*.amount' => 'nullable|numeric',
            'transportItems.*.details' => 'nullable|string',
            'accommodationItems.*.description' => 'nullable|string',
            'accommodationItems.*.amount' => 'nullable|numeric',
            'accommodationItems.*.details' => 'nullable|string',
            'perdiemItems.*.description' => 'nullable|string',
            'perdiemItems.*.amount' => 'nullable|numeric',
            'perdiemItems.*.details' => 'nullable|string',
        ]);

        $travel->update($validatedData);

        // Update related items
        if ($request->has('transportItems')) {
            $travel->transportItems()->delete();
            $travel->transportItems()->createMany($validatedData['transportItems']);
        }

        if ($request->has('accommodationItems')) {
            $travel->accommodationItems()->delete();
            $travel->accommodationItems()->createMany($validatedData['accommodationItems']);
        }

        if ($request->has('perdiemItems')) {
            $travel->perdiemItems()->delete();
            $travel->perdiemItems()->createMany($validatedData['perdiemItems']);
        }

        return redirect()->route('data.show', $travel)->with('success', 'Data berhasil diperbarui.');
    }
}