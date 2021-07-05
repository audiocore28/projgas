<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToBatangasLoadDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('to_batangas_load_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('to_batangas_load_id');
            $table->bigInteger('product_id');
            $table->decimal('quantity', 8, 0)->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('to_batangas_load_id')->references('id')->on('to_batangas_loads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('to_batangas_load_details');
    }
}
