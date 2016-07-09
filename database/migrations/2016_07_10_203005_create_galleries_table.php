<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGalleriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('collection_id')->unsigned();
            $table->string('type', 32);
            $table->string('slug')->unique();
            $table->integer('position')->default(1)->unsigned();
            $table->boolean('visible')->default(1);
            $table->string('admin_order_by', 32)->default('id');
            $table->string('admin_sort', 16)->default('desc');
            $table->boolean('admin_per_page')->default(20);
            $table->string('site_order_by', 32)->default('id');
            $table->string('site_sort', 16)->default('desc');
            $table->boolean('site_per_page')->default(10);
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('collection_id')->references('id')->on('collections');
        });

        Schema::create('gallery_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gallery_id')->unsigned();
            $table->string('language', 3);
            $table->string('title');
            $table->string('meta_desc')->nullable();
            $table->timestamps();

            $table->foreign('gallery_id')->references('id')->on('galleries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('gallery_languages');

        Schema::drop('galleries');
    }
}
