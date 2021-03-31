<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatangasTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batangas_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('trip_no');
            $table->bigInteger('tanker_id');
            $table->bigInteger('driver_id');
            $table->bigInteger('helper_id');
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
        Schema::dropIfExists('batangas_transactions');
    }
}
