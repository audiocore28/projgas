<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelperTankerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('helper_tanker', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('helper_id');
            $table->unsignedBigInteger('tanker_id');
            $table->timestamps();

            $table->unique(['helper_id', 'tanker_id']);

            $table->foreign('helper_id')->references('id')->on('helpers');
            $table->foreign('tanker_id')->references('id')->on('tankers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('helper_tanker');
    }
}
