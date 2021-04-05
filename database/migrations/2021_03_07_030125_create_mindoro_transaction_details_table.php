<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMindoroTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mindoro_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mindoro_transaction_id');
            $table->date('date');
            $table->string('dr_no')->nullable();
            $table->bigInteger('client_id');
            $table->bigInteger('product_id');
            $table->decimal('quantity', 8, 0)->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('mindoro_transaction_id')->references('id')->on('mindoro_transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mindoro_transaction_details');
    }
}
