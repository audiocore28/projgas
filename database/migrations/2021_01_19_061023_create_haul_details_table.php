<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHaulDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('haul_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('haul_id');
            $table->date('date')->nullable();
            $table->bigInteger('client_id')->nullable();
            $table->bigInteger('product_id');
            $table->decimal('quantity', 8, 0)->nullable();
            $table->decimal('unit_price', 10, 3)->nullable();
            $table->timestamps();

            $table->foreign('haul_id')->references('id')->on('hauls')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('haul_details');
    }
}
