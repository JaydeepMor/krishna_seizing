<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfirmCancelFieldsToVehicleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->enum('is_confirm', ['0', '1'])->default('0')->comment('0: Nope, 1: Yes Confirmed')->after('region');
            $table->enum('is_cancel', ['0', '1'])->default('0')->comment('0: Nope, 1: Yes Canceled')->after('is_confirm');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('is_confirm');
        });
    }
}
