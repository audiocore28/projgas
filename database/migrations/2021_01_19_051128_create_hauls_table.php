<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHaulsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hauls', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('purchase_id')->nullable();
            $table->bigInteger('tanker_id')->nullable();
            $table->bigInteger('driver_id')->nullable();
            $table->bigInteger('helper_id')->nullable();
            $table->string('trip_no')->nullable();
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
        Schema::dropIfExists('hauls');
    }
}
