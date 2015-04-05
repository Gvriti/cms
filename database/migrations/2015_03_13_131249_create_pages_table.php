<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('menu_id')->unsigned();
            $table->integer('parent_id')->default(0);
            $table->integer('collection_id')->unsigned()->default(0);
            $table->string('slug')->unique();
            $table->string('type', 32)->default('text');
            $table->integer('position')->default(1)->unsigned();
            $table->boolean('visible')->default(1);
            $table->boolean('collapse')->default(0);
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('menu_id')->references('id')->on('menus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pages');
    }
}
