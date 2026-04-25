<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SPTProgres extends Model
{
    use HasFactory;

    protected $table = 'spt_progres';

    protected $fillable = [
        'travel_id',
        'nomor_spt',
        'nomor_spd',
        'nama_pegawai',
        'laporan_file_id',
        'laporan_file_name',
        'laporan_uploaded_at',
        'penanggung_jawab_file_id',
        'penanggung_jawab_file_name',
        'penanggung_jawab_uploaded_at',
        'pembayaran_file_id',
        'pembayaran_file_name',
        'pembayaran_uploaded_at',
        'google_drive_folder_id',
        'google_drive_folder_name',
        'is_complete',
    ];

    protected $casts = [
        'laporan_uploaded_at' => 'datetime',
        'penanggung_jawab_uploaded_at' => 'datetime',
        'pembayaran_uploaded_at' => 'datetime',
        'is_complete' => 'boolean',
    ];

    /**
     * Relationship dengan Travel
     */
    public function travel()
    {
        return $this->belongsTo(Travel::class);
    }

    /**
     * Check jika semua file sudah di-upload
     */
    public function checkCompletion()
    {
        $isComplete = $this->laporan_file_id && 
                      $this->penanggung_jawab_file_id && 
                      $this->pembayaran_file_id;
        
        $this->update(['is_complete' => (bool) $isComplete]);
        return $isComplete;
    }

    /**
     * Get upload status untuk ketiga file
     */
    public function getFileStatus()
    {
        return [
            'laporan' => (bool) $this->laporan_file_id,
            'penanggung_jawab' => (bool) $this->penanggung_jawab_file_id,
            'pembayaran' => (bool) $this->pembayaran_file_id,
        ];
    }

    /**
     * Get file yang sudah di-upload
     */
    public function getUploadedFiles()
    {
        $files = [];
        
        if ($this->laporan_file_id) {
            $files[] = [
                'type' => 'laporan',
                'name' => $this->laporan_file_name,
                'file_id' => $this->laporan_file_id,
                'uploaded_at' => $this->laporan_uploaded_at,
            ];
        }
        
        if ($this->penanggung_jawab_file_id) {
            $files[] = [
                'type' => 'penanggung_jawab',
                'name' => $this->penanggung_jawab_file_name,
                'file_id' => $this->penanggung_jawab_file_id,
                'uploaded_at' => $this->penanggung_jawab_uploaded_at,
            ];
        }
        
        if ($this->pembayaran_file_id) {
            $files[] = [
                'type' => 'pembayaran',
                'name' => $this->pembayaran_file_name,
                'file_id' => $this->pembayaran_file_id,
                'uploaded_at' => $this->pembayaran_uploaded_at,
            ];
        }
        
        return $files;
    }
}
