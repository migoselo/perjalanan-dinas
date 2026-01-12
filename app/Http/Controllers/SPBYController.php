<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Travel;
use PDF; // from barryvdh/laravel-dompdf, optional
use NumberFormatter;

class SpbyController extends Controller
{
    // Show SPBY in browser (HTML)
    public function show(Travel $travel)
    {
        $travel->load(['transportItems', 'accommodationItems', 'perdiemItems']);

        // Signatories / fixed names (sesuaikan)
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

        // totals
        $transportTotal = $travel->transportItems->sum('amount');
        $hotelTotal = $travel->accommodationItems->sum(function ($i) {
            return ($i->nights ?? 0) * ($i->price ?? 0); });
        $perdiemTotal = $travel->perdiemItems->sum(function ($p) {
            return ($p->days ?? 0) * ($p->amount ?? 0); });
        $grandTotal = $transportTotal + $hotelTotal + $perdiemTotal;

        // terbilang (Indonesian)
        $terbilang = $this->terbilang($grandTotal);

        // contoh di SpbyController@show / @pdf
        return  view('spby.show', compact('travel', 'signatories', 'transportTotal', 'hotelTotal', 'perdiemTotal', 'grandTotal', 'terbilang'));
    }

    // Download PDF
    public function pdf(Travel $travel)
    {
        $travel->load(['transportItems', 'accommodationItems', 'perdiemItems']);

        $signatories = config('spby.signatories');
        $transportTotal = $travel->transportItems->sum('amount');
        $hotelTotal = $travel->accommodationItems->sum(function ($i) {
            return ($i->nights ?? 0) * ($i->price ?? 0); });
        $perdiemTotal = $travel->perdiemItems->sum(function ($p) {
            return ($p->days ?? 0) * ($p->amount ?? 0); });
        $grandTotal = $transportTotal + $hotelTotal + $perdiemTotal;
        $terbilang = $this->terbilang($grandTotal);

        $pdf = PDF::loadView('spby.show', compact(
            'travel',
            'signatories',
            'transportTotal',
            'hotelTotal',
            'perdiemTotal',
            'grandTotal',
            'terbilang'
        ));
        // optional: paper size A4 portrait
        $pdf->setPaper('A4', 'portrait');

        $filename = 'SPBY_' . $travel->id . '_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    // Convert number to Indonesian words (simple implementation)
    private function terbilang($number)
    {
        // handle zero
        if ($number == 0)
            return 'Nol Rupiah';

        $units = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $num = (int) $number;

        $result = $this->toWords($num);
        // capitalize first letter and append "Rupiah"
        $result = trim(preg_replace('/\s+/', ' ', $result));
        return ucfirst(strtolower($result)) . ' Rupiah';
    }

    private function toWords($x)
    {
        $x = (int) $x;
        if ($x < 12)
            return ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'][$x];
        if ($x < 20)
            return $this->toWords($x - 10) . ' Belas';
        if ($x < 100)
            return $this->toWords((int) ($x / 10)) . ' Puluh' . ($x % 10 ? ' ' . $this->toWords($x % 10) : '');
        if ($x < 200)
            return 'Seratus' . ($x - 100 ? ' ' . $this->toWords($x - 100) : '');
        if ($x < 1000)
            return $this->toWords((int) ($x / 100)) . ' Ratus' . ($x % 100 ? ' ' . $this->toWords($x % 100) : '');
        if ($x < 2000)
            return 'Seribu' . ($x - 1000 ? ' ' . $this->toWords($x - 1000) : '');
        if ($x < 1000000)
            return $this->toWords((int) ($x / 1000)) . ' Ribu' . ($x % 1000 ? ' ' . $this->toWords($x % 1000) : '');
        if ($x < 1000000000)
            return $this->toWords((int) ($x / 1000000)) . ' Juta' . ($x % 1000000 ? ' ' . $this->toWords($x % 1000000) : '');
        if ($x < 1000000000000)
            return $this->toWords((int) ($x / 1000000000)) . ' Miliar' . ($x % 1000000000 ? ' ' . $this->toWords($x % 1000000000) : '');
        return ''; // too big (adjust if needed)
    }
}