<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePumpReadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pump_readings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('station_transaction_id');
            $table->string('pump')->nullable();
            $table->decimal('opening', 10, 3)->nullable();
            $table->decimal('closing', 10, 3)->nullable();
            $table->decimal('unit_price', 10, 3)->nullable();
            $table->timestamps();

            $table->foreign('station_transaction_id')->references('id')->on('station_transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pump_readings');
    }
}
