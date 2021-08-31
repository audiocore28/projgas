<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDefaultValueToToBatangasLoadDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('to_batangas_load_details', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('to_batangas_load_details', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->nullable()->change();
        });
    }
}
