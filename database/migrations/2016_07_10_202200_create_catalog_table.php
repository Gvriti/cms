<?php

use Illuminate\Support\Facades\Schema;
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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('collection_id');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('position')->default(1);
            $table->boolean('visible')->default(1);
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('collection_id')->references('id')->on('collections');
        });

        Schema::create('catalog_languages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('catalog_id');
            $table->char('language', 2);
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
