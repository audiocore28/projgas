<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('delivery_id');
            $table->date('date')->nullable();
            $table->string('dr_no');
            $table->bigInteger('client_id')->nullable();
            $table->bigInteger('product_id');
            $table->decimal('quantity', 8, 0)->nullable();
            $table->decimal('unit_price', 10, 3)->nullable();
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
        Schema::dropIfExists('delivery_details');
    }
}
