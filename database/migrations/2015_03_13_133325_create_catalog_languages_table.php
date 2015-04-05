<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('catalog_id')->unsigned();
            $table->string('language', 3);
            $table->string('title');
            $table->string('short_title');
            $table->text('description');
            $table->mediumText('content');
            $table->string('meta_desc')->nullable();
            $table->timestamps();

            $table->foreign('catalog_id')->references('id')->on('catalog')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('catalog_languages');
    }
}
