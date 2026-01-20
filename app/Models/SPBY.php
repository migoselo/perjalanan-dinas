<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SPBY extends Model
{
    protected $table = 'spbys';
    
    protected $fillable = [
        'travel_id',
        'tanggal_spby',
        'nomor_spby',
        'jumlah_pembayaran',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_spby' => 'date',
        'jumlah_pembayaran' => 'decimal:2',
    ];

    public function travel()
    {
        return $this->belongsTo(Travel::class);
    }
}
