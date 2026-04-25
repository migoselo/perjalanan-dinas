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
        'lembar_ke',
        'kode_no',
        'nomor_spd',
        'pejabat_pemberi_perintah',
        'nama_pegawai',
        'nip_pegawai',
        'pangkat',
        'jabatan',
        'tingkat_biaya',
        'maksud_perjalanan',
        'alat_angkutan',
        'tempat_berangkat',
        'tempat_tujuan',
        'lama_perjalanan',
        'tanggal_berangkat',
        'tanggal_kembali',
        'instansi_pembebanan',
        'mata_anggaran',
        'tempat_penerbitan',
        'tanggal_penerbitan',
        'jabatan_penandatangan',
        'nama_penandatangan',
        'nip_penandatangan',
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
