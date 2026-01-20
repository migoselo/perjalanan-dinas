<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Travel;
use App\Models\SPBY;
use Carbon\Carbon;
// Facade DomPDF yang umum digunakan untuk barryvdh/laravel-dompdf v3.x
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Log;

class SpbyController extends Controller
{
    // Show SPBY form with preview
    public function form(Travel $travel, Request $request)
    {
        // Set Carbon locale to Indonesian
        Carbon::setLocale('id');

        $travel->load(['transportItems', 'accommodationItems', 'perdiemItems']);

        // Check if this is a POST request (form submit)
        if ($request->isMethod('post')) {
            try {
                // Validate and save SPBY data
                $validated = $request->validate([
                    'tanggal_spby' => 'required|date',
                    'nomor_spby' => 'required|string',
                    'untuk_pembayaran' => 'required|string',
                    'jumlah_pembayaran' => 'required|numeric|min:0',
                ]);

                // Save or update SPBY for this travel
                SPBY::updateOrCreate(
                    ['travel_id' => $travel->id],
                    [
                        'tanggal_spby' => $validated['tanggal_spby'],
                        'nomor_spby' => $validated['nomor_spby'],
                        'keterangan' => $validated['untuk_pembayaran'],
                        'jumlah_pembayaran' => $validated['jumlah_pembayaran'],
                    ]
                );

                // If AJAX request, return JSON
                if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json(['success' => true, 'message' => 'SPBY berhasil disimpan']);
                }

                return redirect()->route('data.spby', $travel)->with('success', 'SPBY berhasil disimpan');
            } catch (\Illuminate\Validation\ValidationException $e) {
                // If AJAX request, return validation errors as JSON
                if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json([
                        'success' => false,
                        'errors' => $e->errors()
                    ], 422);
                }
                throw $e;
            }
        }

        // Get form data or defaults from database or form input
        $spbyRecord = SPBY::where('travel_id', $travel->id)->first();
        
        $formData = [
            'date' => $request->input('date', $spbyRecord?->tanggal_spby?->format('Y-m-d') ?? now()->format('Y-m-d')),
            'number' => $request->input('number', $spbyRecord?->nomor_spby ?? ''),
            'purpose' => $request->input('purpose', $spbyRecord?->keterangan ?? $travel->uraian_kegiatan ?? "Perjalanan Dinas ke " . ($travel->destination ?? 'destinasi')),
            'amount' => $request->input('amount', (int)($spbyRecord?->jumlah_pembayaran ?? $this->calculateTotal($travel))),
        ];

        // Prepare SPBY data with current form inputs
        $dateObj = Carbon::parse($formData['date']);
        $amount = (int) $formData['amount'];
        
        // Format: "15 Januari 2026" untuk tanggal dan "Januari 2026" untuk nomor
        $dayMonthYear = $dateObj->isoFormat('D MMMM YYYY');
        $monthYear = $dateObj->isoFormat('MMMM YYYY');
        
        // Get signatories from config (same as show method)
        $signatories = config('spby.signatories', [
            'bendahara' => [
                'name' => 'ISWANTONO',
                'nip' => '197305062006041004',
                'position' => 'Bendahara Pengeluaran',
            ],
            'penerima' => [
                'name' => 'NABILAH',
                'nip' => '200109252025042001',
                'position' => 'Penerima Uang/Uang Muka Kerja',
            ],
            'ppk' => [
                'name' => 'DENI WIJAYANTO',
                'nip' => '198005072006041005',
                'position' => 'Pejabat Pembuat Komitmen',
            ],
        ]);
        
        $transportTotal = $travel->transportItems->sum('amount');
        $hotelTotal = $travel->accommodationItems->sum(function ($i) {
            return ($i->nights ?? 0) * ($i->price ?? 0);
        });
        $perdiemTotal = $travel->perdiemItems->sum(function ($p) {
            return ($p->days ?? 0) * ($p->amount ?? 0);
        });
        $grandTotal = $transportTotal + $hotelTotal + $perdiemTotal;
        
        $spbyData = [
            'date' => $dayMonthYear,
            'number' => $formData['number'] ? $formData['number'] . '/SPBY/' . $monthYear : '___/SPBY/' . $monthYear,
            'amount' => number_format($amount, 0, ',', '.') . ',-',
            'amount_in_words' => $this->terbilang($amount),
            'recipient_name' => 'NABILAH',
            'transport_total' => $transportTotal,
            'hotel_total' => $hotelTotal,
            'perdiem_total' => $perdiemTotal,
            'purpose' => $formData['purpose'],
            'activity_mak' => '7437.BAH.078.101.C.524119',
            'code' => '-',
            'signatures' => [
                ['role' => $signatories['bendahara']['position'], 'name' => $signatories['bendahara']['name'], 'nip' => $signatories['bendahara']['nip']],
                ['role' => $signatories['penerima']['position'], 'name' => $signatories['penerima']['name'], 'nip' => $signatories['penerima']['nip']],
                ['role' => $signatories['ppk']['position'], 'name' => $signatories['ppk']['name'], 'nip' => $signatories['ppk']['nip']],
            ],
            'travel' => $travel,
        ];

        return view('SPBY.form', compact('travel', 'formData', 'spbyData'));
    }

    private function calculateTotal($travel)
    {
        $transportTotal = $travel->transportItems->sum('amount');
        $hotelTotal = $travel->accommodationItems->sum(function ($i) {
            return ($i->nights ?? 0) * ($i->price ?? 0);
        });
        $perdiemTotal = $travel->perdiemItems->sum(function ($p) {
            return ($p->days ?? 0) * ($p->amount ?? 0);
        });
        
        return $transportTotal + $hotelTotal + $perdiemTotal;
    }

    // Show SPBY list
    public function index()
    {
        $travels = Travel::all();
        return view('spby.index', compact('travels'));
    }

    // Show SPBY in browser (HTML)
    public function show(Travel $travel)
    {
        $travel->load(['transportItems', 'accommodationItems', 'perdiemItems']);

        // Get SPBY data from database
        $spbyRecord = SPBY::where('travel_id', $travel->id)->first();

        $signatories = config('spby.signatories', [
            'bendahara' => [
                'name' => 'ISWANTONO',
                'nip' => '197305062006041004',
                'position' => 'Bendahara Pengeluaran',
            ],
            'penerima' => [
                'name' => 'NABILAH',
                'nip' => '200109252025042001',
                'position' => 'Penerima Uang/Uang Muka Kerja',
            ],
            'ppk' => [
                'name' => 'DENI WIJAYANTO',
                'nip' => '198005072006041005',
                'position' => 'Pejabat Pembuat Komitmen',
            ],
        ]);

        $transportTotal = $travel->transportItems->sum('amount');
        $hotelTotal = $travel->accommodationItems->sum(function ($i) {
            return ($i->nights ?? 0) * ($i->price ?? 0);
        });
        $perdiemTotal = $travel->perdiemItems->sum(function ($p) {
            return ($p->days ?? 0) * ($p->amount ?? 0);
        });
        // Use saved amount if exists, otherwise use grand total
        $grandTotal = $spbyRecord?->jumlah_pembayaran ?? ($transportTotal + $hotelTotal + $perdiemTotal);

        $terbilang = $this->terbilang($grandTotal);

        return view('spby.show', compact(
            'travel',
            'signatories',
            'transportTotal',
            'hotelTotal',
            'perdiemTotal',
            'grandTotal',
            'terbilang',
            'spbyRecord'
        ));
    }

    // API endpoint to get latest SPBY data (for AJAX)
    public function getLatestData(Travel $travel)
    {
        $travel->load(['transportItems', 'accommodationItems', 'perdiemItems']);

        // Get SPBY data from database
        $spbyRecord = SPBY::where('travel_id', $travel->id)->first();

        $signatories = config('spby.signatories', [
            'bendahara' => [
                'name' => 'ISWANTONO',
                'nip' => '197305062006041004',
                'position' => 'Bendahara Pengeluaran',
            ],
            'penerima' => [
                'name' => 'NABILAH',
                'nip' => '200109252025042001',
                'position' => 'Penerima Uang/Uang Muka Kerja',
            ],
            'ppk' => [
                'name' => 'DENI WIJAYANTO',
                'nip' => '198005072006041005',
                'position' => 'Pejabat Pembuat Komitmen',
            ],
        ]);

        $transportTotal = $travel->transportItems->sum('amount');
        $hotelTotal = $travel->accommodationItems->sum(function ($i) {
            return ($i->nights ?? 0) * ($i->price ?? 0);
        });
        $perdiemTotal = $travel->perdiemItems->sum(function ($p) {
            return ($p->days ?? 0) * ($p->amount ?? 0);
        });
        // Use saved amount if exists, otherwise use grand total
        $grandTotal = $spbyRecord?->jumlah_pembayaran ?? ($transportTotal + $hotelTotal + $perdiemTotal);

        $terbilang = $this->terbilang($grandTotal);

        return response()->json([
            'travel' => $travel,
            'signatories' => $signatories,
            'transportTotal' => $transportTotal,
            'hotelTotal' => $hotelTotal,
            'perdiemTotal' => $perdiemTotal,
            'grandTotal' => $grandTotal,
            'terbilang' => $terbilang,
            'spbyRecord' => $spbyRecord
        ]);
    }


    // Download PDF
    public function pdf(Travel $travel)
    {
        $travel->load(['transportItems', 'accommodationItems', 'perdiemItems']);

        $signatories = config('spby.signatories');
        $transportTotal = $travel->transportItems->sum('amount');
        $hotelTotal = $travel->accommodationItems->sum(function ($i) {
            return ($i->nights ?? 0) * ($i->price ?? 0);
        });
        $perdiemTotal = $travel->perdiemItems->sum(function ($p) {
            return ($p->days ?? 0) * ($p->amount ?? 0);
        });
        $grandTotal = $transportTotal + $hotelTotal + $perdiemTotal;
        $terbilang = $this->terbilang($grandTotal);

        $data = compact(
            'travel',
            'signatories',
            'transportTotal',
            'hotelTotal',
            'perdiemTotal',
            'grandTotal',
            'terbilang'
        );

        // Pastikan view bisa dirender dulu (lebih mudah cari sumber error)
        try {
            $html = view('spby.show', $data)->render();
        } catch (\Throwable $e) {
            Log::error('SPBY view render error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors('Gagal merender tampilan SPBY: ' . $e->getMessage());
        }

        try {
            // Jika facade tersedia gunakan loadView, jika tidak gunakan dompdf.wrapper
            if (class_exists(\Barryvdh\DomPDF\Facade::class)) {
                $pdf = PDF::loadView('spby.show', $data);
            } else {
                $pdf = app('dompdf.wrapper');
                $pdf->loadHTML($html);
            }

            $pdf->setPaper('A4', 'portrait');

            $filename = 'SPBY_' . $travel->id . '_' . now()->format('Ymd_His') . '.pdf';
            return $pdf->download($filename);
        } catch (\Throwable $e) {
            Log::error('SPBY pdf generation error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors('Gagal menghasilkan PDF: ' . $e->getMessage());
        }
    }

    // Convert number to Indonesian words (simple implementation)
    private function terbilang($number)
    {
        if ($number == 0) {
            return 'Nol Rupiah';
        }

        $num = (int) $number;
        $result = $this->toWords($num);
        $result = trim(preg_replace('/\s+/', ' ', $result));
        return ucfirst(strtolower($result)) . ' Rupiah';
    }

    private function toWords($x)
    {
        $x = (int) $x;
        if ($x < 12) {
            return ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'][$x];
        }
        if ($x < 20) {
            return $this->toWords($x - 10) . ' Belas';
        }
        if ($x < 100) {
            return $this->toWords((int) ($x / 10)) . ' Puluh' . ($x % 10 ? ' ' . $this->toWords($x % 10) : '');
        }
        if ($x < 200) {
            return 'Seratus' . ($x - 100 ? ' ' . $this->toWords($x - 100) : '');
        }
        if ($x < 1000) {
            return $this->toWords((int) ($x / 100)) . ' Ratus' . ($x % 100 ? ' ' . $this->toWords($x % 100) : '');
        }
        if ($x < 2000) {
            return 'Seribu' . ($x - 1000 ? ' ' . $this->toWords($x - 1000) : '');
        }
        if ($x < 1000000) {
            return $this->toWords((int) ($x / 1000)) . ' Ribu' . ($x % 1000 ? ' ' . $this->toWords($x % 1000) : '');
        }
        if ($x < 1000000000) {
            return $this->toWords((int) ($x / 1000000)) . ' Juta' . ($x % 1000000 ? ' ' . $this->toWords($x % 1000000) : '');
        }
        if ($x < 1000000000000) {
            return $this->toWords((int) ($x / 1000000000)) . ' Miliar' . ($x % 1000000000 ? ' ' . $this->toWords($x % 1000000000) : '');
        }
        return '';
    }
}