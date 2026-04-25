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
        Schema::table('travels', function (Blueprint $table) {
            if (!Schema::hasColumn('travels', 'pemberi_uang')) {
                $table->string('pemberi_uang')->nullable()->after('bukti_kas');
            }
            if (!Schema::hasColumn('travels', 'tanggal_pembayaran')) {
                $table->date('tanggal_pembayaran')->nullable()->after('pemberi_uang');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travels', function (Blueprint $table) {
            if (Schema::hasColumn('travels', 'tanggal_pembayaran')) {
                $table->dropColumn('tanggal_pembayaran');
            }
            if (Schema::hasColumn('travels', 'pemberi_uang')) {
                $table->dropColumn('pemberi_uang');
            }
        });
    }
};
