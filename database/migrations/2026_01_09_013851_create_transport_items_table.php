<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportItemsTable extends Migration
{
    public function up()
    {
        Schema::create('transport_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_id')->constrained('travels')->onDelete('cascade');
            $table->string('mode')->nullable();        // transportasi: Pesawat, Kereta, dll
            $table->string('description')->nullable(); // uraian perjalanan
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transport_items');
    }
}