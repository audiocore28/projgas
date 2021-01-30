<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTankerLoadDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tanker_load_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tanker_load_id');
            $table->bigInteger('product_id')->nullable();
            $table->decimal('quantity', 8, 0)->nullable();
            $table->timestamps();

            $table->foreign('tanker_load_id')->references('id')->on('tanker_loads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tanker_load_details');
    }
}
