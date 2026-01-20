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
        Schema::create('spbys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_id');
            $table->date('tanggal_spby')->nullable();
            $table->string('nomor_spby')->nullable();
            $table->decimal('jumlah_pembayaran', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Add foreign key constraint
            $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spbys');
    }
};
