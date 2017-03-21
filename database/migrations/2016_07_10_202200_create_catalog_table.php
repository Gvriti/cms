<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('collection_id')->unsigned();
            $table->string('slug')->unique();
            $table->integer('position')->default(1)->unsigned();
            $table->boolean('visible')->default(1);
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('collection_id')->references('id')->on('collections');
        });

        Schema::create('catalog_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('catalog_id')->unsigned();
            $table->string('language', 3);
            $table->string('title');
            $table->text('description')->nullable();
            $table->mediumText('content')->nullable();
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
        Schema::dropIfExists('catalog_languages');

        Schema::dropIfExists('catalog');
    }
}
