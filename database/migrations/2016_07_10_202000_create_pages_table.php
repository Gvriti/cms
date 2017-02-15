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
            $table->string('slug')->unique();
            $table->integer('position')->default(1)->unsigned();
            $table->integer('parent_id')->default(0)->unsigned();
            $table->string('type', 64)->default('text');
            $table->integer('type_id')->default(0)->unsigned();
            $table->string('template', 64)->nullable();
            $table->boolean('visible')->default(1);
            $table->boolean('collapse')->default(0);
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('menu_id')->references('id')->on('menus');
        });

        Schema::create('page_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('page_id')->unsigned();
            $table->string('language', 3);
            $table->string('title');
            $table->string('short_title');
            $table->text('description');
            $table->mediumText('content');
            $table->string('meta_desc')->nullable();
            $table->timestamps();

            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_languages');

        Schema::dropIfExists('pages');
    }
}
