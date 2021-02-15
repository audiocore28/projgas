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
            $table->string('pump_no')->nullable();
            $table->string('voucher_no')->nullable();
            $table->bigInteger('client_id')->nullable();
            $table->bigInteger('product_id');
            $table->decimal('quantity', 8, 3)->nullable();
            $table->bigInteger('station_transaction_id');
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
        Schema::dropIfExists('company_vales');
    }
}
