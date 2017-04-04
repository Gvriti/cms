<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model_name', 64)->index();
            $table->unsignedInteger('model_id')->index();
            $table->unsignedSmallInteger('position')->default(1);
            $table->boolean('visible')->default(1);
            $table->timestamps();
        });

        Schema::create('file_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('file_id');
            $table->string('language', 3);
            $table->string('title');
            $table->string('file', 800)->nullable();
            $table->timestamps();

            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_languages');

        Schema::dropIfExists('files');
    }
}
