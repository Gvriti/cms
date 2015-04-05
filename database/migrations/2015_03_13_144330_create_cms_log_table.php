<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip', 32);
            $table->integer('cms_user_id')->unsigned();
            $table->integer('page_id');
            $table->string('request', 800);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('cms_user_id')->references('id')->on('cms_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cms_log');
    }
}
