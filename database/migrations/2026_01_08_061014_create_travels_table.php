<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravelsTable extends Migration
{
    public function up()
    {
        Schema::create('travels', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_spd')->nullable();
            $table->string('nomor_surat_tugas')->nullable();
            $table->date('tanggal_spd')->nullable();
            $table->string('sumber_dana')->nullable();
            $table->string('kode_mak')->nullable();
            $table->string('nama_pegawai')->nullable();
            $table->string('bukti_kas')->nullable();
            $table->text('uraian_kegiatan')->nullable();
            $table->decimal('daily_allowance', 15, 2)->nullable();
            $table->integer('days_count')->nullable();
            $table->json('extra')->nullable(); // jika mau simpan transportasi/penginapan sebagai JSON
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('travels');
    }
}