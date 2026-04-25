<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Travel extends Model
{
    use HasFactory;

    protected $table = 'travels';

    protected $fillable = [
        'nomor_spd','nomor_surat_tugas','tanggal_spd','sumber_dana','kode_mak',
        'nama_pegawai','nip','bukti_kas','uraian_kegiatan','user_id',
        // kuitansi fields
        'pemberi_uang','tanggal_pembayaran',
        // pengikut stored as JSON
        'pengikut',
        // stored surat form data
        'surat_data',
    ];

    protected $casts = [
        'pengikut' => 'array',
        'surat_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function spby()
    {
        return $this->hasOne(SPBY::class);
    }

    public function transportItems()
    {
        return $this->hasMany(TransportItem::class);
    }

    public function accommodationItems()
    {
        return $this->hasMany(AccommodationItem::class);
    }

    public function perdiemItems()
    {
        return $this->hasMany(PerdiemItem::class);
    }

    public function sptProgres()
    {
        return $this->hasMany(SPTProgres::class);
    }

    // helper to compute totals
    public function getTransportTotalAttribute()
    {
        return $this->transportItems->sum('amount');
    }

    public function getAccommodationTotalAttribute()
    {
        return $this->accommodationItems->sum(function($i){ return $i->nights * $i->price; });
    }

    public function getPerdiemTotalAttribute()
    {
        return $this->perdiemItems->sum(function($p){ return $p->days * $p->amount; });
    }

    public function getGrandTotalAttribute()
    {
        return $this->transport_total + $this->accommodation_total + $this->perdiem_total;
    }
}