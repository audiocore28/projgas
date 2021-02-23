<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStationTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('station_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('station_id')->nullable();
            $table->date('date')->nullable();
            $table->string('shift')->nullable();
            $table->bigInteger('cashier_id')->nullable();
            $table->bigInteger('pump_attendant_id')->nullable();
            $table->bigInteger('office_staff_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('station_transactions');
    }
}
