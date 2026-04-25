<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuratDataToTravelsTable extends Migration
{
    public function up()
    {
        Schema::table('travels', function (Blueprint $table) {
            $table->json('surat_data')->nullable();
        });
    }

    public function down()
    {
        Schema::table('travels', function (Blueprint $table) {
            $table->dropColumn('surat_data');
        });
    }
}
