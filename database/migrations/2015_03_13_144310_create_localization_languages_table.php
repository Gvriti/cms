<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalizationLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('localization_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('localization_id')->unsigned();
            $table->string('language', 3);
            $table->string('value');
            $table->timestamps();

            $table->foreign('localization_id')->references('id')
                                              ->on('localization')
                                              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('localization_languages');
    }
}
