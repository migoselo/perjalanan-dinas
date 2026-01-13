<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Travel;
// Facade DomPDF yang umum digunakan untuk barryvdh/laravel-dompdf v3.x
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Log;

class SpbyController extends Controller
{
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

        $signatories = config('spby.signatories', [
            'bendahara' => [
                'name' => 'ISWANTONO',
                'nip' => '197305062006041004',
                'position' => 'Bendahara Pengeluaran',
            ],
            'penerima' => [
                'name' => 'NABILILAH',
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

        $terbilang = $this->terbilang($grandTotal);

        return view('spby.show', compact(
            'travel',
            'signatories',
            'transportTotal',
            'hotelTotal',
            'perdiemTotal',
            'grandTotal',
            'terbilang'
        ));
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