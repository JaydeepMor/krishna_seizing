<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('loan_number')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('model')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('chassis_number')->nullable();
            $table->string('engine_number')->nullable();
            $table->string('arm_rrm')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('brm')->nullable();
            $table->string('final_confirmation')->nullable();
            $table->string('final_manager_name')->nullable();
            $table->string('final_manager_mobile_number')->nullable();
            $table->text('address')->nullable();
            $table->string('branch')->nullable();
            $table->string('bkt')->nullable();
            $table->string('area')->nullable();
            $table->string('region')->nullable();
            $table->integer('lot_number')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');

        $table->softDeletes();
    }
}
