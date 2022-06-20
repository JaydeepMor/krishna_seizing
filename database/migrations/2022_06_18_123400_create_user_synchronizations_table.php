<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSynchronizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_synchronizations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('finance_company_id')->unsigned()->nullable();
            $table->foreign('finance_company_id')->references('id')->on('finance_companies')->onDelete('cascade');
            $table->double('vehicle_count')->default(0);
            $table->enum('is_synced', ['0', '1'])->default('0')->comment('0: Nope, 1: Yes');
            $table->enum('is_deleted', ['0', '1'])->default('0')->comment('0: Nope, 1: Yes');
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
        Schema::dropIfExists('user_synchronizations');
    }
}
