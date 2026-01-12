
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerdiemItemsTable extends Migration
{
    public function up()
    {
        Schema::create('perdiem_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_id')->constrained('travels')->onDelete('cascade');
            $table->string('city')->nullable();
            $table->integer('days')->default(0);
            $table->decimal('amount', 15, 2)->default(0); // per day
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('perdiem_items');
    }
}