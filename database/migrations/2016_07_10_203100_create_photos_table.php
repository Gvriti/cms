<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gallery_id')->unsigned();
            $table->integer('position')->default(1)->unsigned();
            $table->boolean('visible')->default(1);
            $table->string('file', 800)->nullable();
            $table->timestamps();

            $table->foreign('gallery_id')->references('id')->on('galleries');
        });

        Schema::create('photo_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('photo_id')->unsigned();
            $table->string('language', 3);
            $table->string('title');
            $table->timestamps();

            $table->foreign('photo_id')->references('id')->on('photos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('photo_languages');

        Schema::drop('photos');
    }
}
