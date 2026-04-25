<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('spbys', function (Blueprint $table) {
            // Tambah kolom untuk SPD form jika belum ada
            if (!Schema::hasColumn('spbys', 'lembar_ke')) {
                $table->string('lembar_ke')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'kode_no')) {
                $table->string('kode_no')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'nomor_spd')) {
                $table->string('nomor_spd')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'pejabat_pemberi_perintah')) {
                $table->text('pejabat_pemberi_perintah')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'nama_pegawai')) {
                $table->string('nama_pegawai')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'nip_pegawai')) {
                $table->string('nip_pegawai')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'pangkat')) {
                $table->string('pangkat')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'jabatan')) {
                $table->string('jabatan')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'tingkat_biaya')) {
                $table->string('tingkat_biaya')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'maksud_perjalanan')) {
                $table->text('maksud_perjalanan')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'alat_angkutan')) {
                $table->string('alat_angkutan')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'tempat_berangkat')) {
                $table->string('tempat_berangkat')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'tempat_tujuan')) {
                $table->string('tempat_tujuan')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'lama_perjalanan')) {
                $table->integer('lama_perjalanan')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'tanggal_berangkat')) {
                $table->date('tanggal_berangkat')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'tanggal_kembali')) {
                $table->date('tanggal_kembali')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'instansi_pembebanan')) {
                $table->string('instansi_pembebanan')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'mata_anggaran')) {
                $table->string('mata_anggaran')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'tempat_penerbitan')) {
                $table->string('tempat_penerbitan')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'tanggal_penerbitan')) {
                $table->date('tanggal_penerbitan')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'jabatan_penandatangan')) {
                $table->string('jabatan_penandatangan')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'nama_penandatangan')) {
                $table->string('nama_penandatangan')->nullable();
            }
            if (!Schema::hasColumn('spbys', 'nip_penandatangan')) {
                $table->string('nip_penandatangan')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spbys', function (Blueprint $table) {
            $columns = [
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
            foreach ($columns as $column) {
                if (Schema::hasColumn('spbys', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
