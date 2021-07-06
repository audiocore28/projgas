<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToMindoroLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('to_mindoro_loads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mindoro_transaction_id')->nullable();
            $table->unsignedBigInteger('purchase_id');
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreign('mindoro_transaction_id')->references('id')->on('mindoro_transactions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('to_mindoro_loads');
    }
}
