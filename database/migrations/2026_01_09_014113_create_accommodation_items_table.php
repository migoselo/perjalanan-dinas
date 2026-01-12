<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccommodationItemsTable extends Migration
{
    public function up()
    {
        Schema::create('accommodation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_id')->constrained('travels')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->integer('nights')->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('accommodation_items');
    }
}