<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Travel;
use PDF; // jika pakai barryvdh/laravel-dompdf

class SPBYController extends Controller
{
    private function defaultSignatories()
    {
        return [
            'bendahara' => [
                'name' => 'ISWANTONO',
                'nip'  => '197305062006041004',
                'position' => 'Bendahara Pengeluaran',
            ],
            'penerima' => [
                'name' => 'NABILILAH',
                'nip'  => '200109252025042001',
                'position' => 'Penerima Uang/Uang Muka Kerja',
            ],
            'ppk' => [
                'name' => 'DENI WIJAYANTO',
                'nip'  => '198005072006041005',
                'position' => 'Pejabat Pembuat Komitmen',
            ],
        ];
    }

    private function getSignatories()
    {
        $cfg = config('spby.signatories');
        if (!is_array($cfg) || empty($cfg)) {
            return $this->defaultSignatories();
        }

        // merge missing keys with defaults
        $default = $this->defaultSignatories();
        foreach ($default as $k => $v) {
            if (empty($cfg[$k]) || !is_array($cfg[$k])) {
                $cfg[$k] = $v;
            } else {
                $cfg[$k]['name'] = $cfg[$k]['name'] ?? $v['name'];
                $cfg[$k]['nip']  = $cfg[$k]['nip'] ?? $v['nip'];
                $cfg[$k]['position'] = $cfg[$k]['position'] ?? $v['position'];
            }
        }

        return $cfg;
    }

    // list SPBY (optional)
    public function index()
    {
        $travels = Travel::with(['transportItems','accommodationItems','perdiemItems'])
                    ->orderByDesc('created_at')->get();

        return view('SPBY.index', compact('travels'));
    }

    // show HTML SPBY
    public function show(Travel $travel)
    {
        $travel->load(['transportItems','accommodationItems','perdiemItems']);

        $signatories = $this->getSignatories();

        $transportTotal = $travel->transportItems->sum('amount');
        $hotelTotal = $travel->accommodationItems->sum(function($i){ return ($i->nights ?? 0) * ($i->price ?? 0); });
        $perdiemTotal = $travel->perdiemItems->sum(function($p){ return ($p->days ?? 0) * ($p->amount ?? 0); });
        $grandTotal = $transportTotal + $hotelTotal + $perdiemTotal;

        $terbilang = $this->terbilang($grandTotal);

        return view('SPBY.show', compact('travel','signatories','transportTotal','hotelTotal','perdiemTotal','grandTotal','terbilang'));
    }

    // download PDF
    public function pdf(Travel $travel)
    {
        $travel->load(['transportItems','accommodationItems','perdiemItems']);

        $signatories = $this->getSignatories();

        $transportTotal = $travel->transportItems->sum('amount');
        $hotelTotal = $travel->accommodationItems->sum(function($i){ return ($i->nights ?? 0) * ($i->price ?? 0); });
        $perdiemTotal = $travel->perdiemItems->sum(function($p){ return ($p->days ?? 0) * ($p->amount ?? 0); });
        $grandTotal = $transportTotal + $hotelTotal + $perdiemTotal;

        $terbilang = $this->terbilang($grandTotal);

        $pdf = PDF::loadView('SPBY.show', compact('travel','signatories','transportTotal','hotelTotal','perdiemTotal','grandTotal','terbilang'));
        $pdf->setPaper('A4','portrait');

        $filename = 'SPBY_' . $travel->id . '_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    private function terbilang($number)
    {
        if ((int)$number === 0) return 'Nol Rupiah';
        $result = $this->toWords((int)$number);
        return trim($result) . ' Rupiah';
    }

    private function toWords($x)
    {
        $x = (int)$x;
        $words = ['', 'Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas'];
        if ($x < 12) return $words[$x];
        if ($x < 20) return $this->toWords($x - 10).' Belas';
        if ($x < 100) return $this->toWords((int)($x/10)).' Puluh'.($x%10? ' '.$this->toWords($x%10):'');
        if ($x < 200) return 'Seratus'.($x-100? ' '.$this->toWords($x-100):'');
        if ($x < 1000) return $this->toWords((int)($x/100)).' Ratus'.($x%100? ' '.$this->toWords($x%100):'');
        if ($x < 2000) return 'Seribu'.($x-1000? ' '.$this->toWords($x-1000):'');
        if ($x < 1000000) return $this->toWords((int)($x/1000)).' Ribu'.($x%1000? ' '.$this->toWords($x%1000):'');
        if ($x < 1000000000) return $this->toWords((int)($x/1000000)).' Juta'.($x%1000000? ' '.$this->toWords($x%1000000):'');
        return '';
    }
}