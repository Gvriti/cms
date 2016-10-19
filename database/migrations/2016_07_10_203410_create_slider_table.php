<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSliderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slider', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('position')->default(1)->unsigned();
            $table->boolean('visible')->default(1);
            $table->string('file', 800)->nullable();
            $table->string('link', 800)->nullable();
            $table->timestamps();
        });

        Schema::create('slider_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('slider_id')->unsigned();
            $table->string('language', 3);
            $table->string('title');
            $table->string('description', 800);
            $table->timestamps();

            $table->foreign('slider_id')->references('id')->on('slider')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slider_languages');

        Schema::dropIfExists('slider');
    }
}
