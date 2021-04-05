<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyValesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_vales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('station_transaction_id');
            $table->string('pump_no')->nullable();
            $table->string('voucher_no')->nullable();
            $table->bigInteger('company_id');
            $table->bigInteger('product_id');
            $table->decimal('quantity', 8, 3)->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('company_vales');
    }
}
