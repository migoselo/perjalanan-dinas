<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPengikutToTravelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('travels', function (Blueprint $table) {
            if (!Schema::hasColumn('travels', 'pengikut')) {
                // 'user_id' column does not exist in original travels table, avoid ->after()
                $table->json('pengikut')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('travels', function (Blueprint $table) {
            if (Schema::hasColumn('travels', 'pengikut')) {
                $table->dropColumn('pengikut');
            }
        });
    }
}
