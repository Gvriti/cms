<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('firstname', 128);
            $table->string('lastname', 128);
            $table->string('phone', 32)->nullable();
            $table->string('address')->nullable();
            $table->boolean('active')->default(1);
            $table->string('password', 128);
            $table->string('remember_token', 128)->nullable();
            $table->string('reset_token', 128)->nullable();
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
        Schema::drop('users');
    }
}
