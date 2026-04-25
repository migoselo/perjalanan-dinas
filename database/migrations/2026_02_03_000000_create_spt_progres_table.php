<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spt_progres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_id')->constrained('travels')->onDelete('cascade');
            $table->string('nomor_spt')->nullable(); // Nomor SPT
            $table->string('nomor_spd')->nullable(); // Nomor SPD
            $table->string('nama_pegawai')->nullable(); // Nama
            
            // File upload tracking
            $table->string('laporan_file_id')->nullable(); // Google Drive File ID - Laporan
            $table->string('laporan_file_name')->nullable();
            $table->datetime('laporan_uploaded_at')->nullable();
            
            $table->string('penanggung_jawab_file_id')->nullable(); // Google Drive File ID - Penanggung Jawab
            $table->string('penanggung_jawab_file_name')->nullable();
            $table->datetime('penanggung_jawab_uploaded_at')->nullable();
            
            $table->string('pembayaran_file_id')->nullable(); // Google Drive File ID - Pembayaran
            $table->string('pembayaran_file_name')->nullable();
            $table->datetime('pembayaran_uploaded_at')->nullable();
            
            // Folder tracking
            $table->string('google_drive_folder_id')->nullable(); // Google Drive Folder ID untuk SPT ini
            $table->string('google_drive_folder_name')->nullable();
            
            $table->boolean('is_complete')->default(false); // Lengkap jika semua 3 file sudah upload
            $table->timestamps();
            
            $table->index('travel_id');
            $table->index('nomor_spt');
            $table->index('is_complete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spt_progres');
    }
};
