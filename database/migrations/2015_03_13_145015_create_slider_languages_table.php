<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSliderLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
        Schema::drop('slider_languages');
    }
}
