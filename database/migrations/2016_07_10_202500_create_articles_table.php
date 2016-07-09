<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('collection_id')->unsigned();
            $table->string('slug')->unique();
            $table->integer('position')->default(1)->unsigned();
            $table->boolean('visible')->default(1);
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('collection_id')->references('id')->on('collections');
        });

        Schema::create('article_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('article_id')->unsigned();
            $table->string('language', 3);
            $table->string('title');
            $table->text('description');
            $table->mediumText('content');
            $table->string('meta_desc')->nullable();
            $table->timestamps();

            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('article_languages');

        Schema::drop('articles');
    }
}
