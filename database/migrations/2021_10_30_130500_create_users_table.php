<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('email')->unique();
            $table->string('contact_number')->nullable();
            $table->string('team_leader')->nullable();
            $table->string('imei_number');
            $table->string('password')->nullable();
            $table->enum('status', ['0', '1'])->default('0')->comment('0: Inactive, 1: Active');
            $table->enum('is_admin', ['0', '1'])->default('0')->comment('0: Nope, 1: Yes');
            $table->bigInteger('group_id')->unsigned()->nullable();
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->rememberToken();
            $table->timestamps();
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
        Schema::dropIfExists('users');

        $table->softDeletes();
    }
}
