<?php

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
            $table->integer('route_id')->index();
            $table->string('route_name', 128);
            $table->smallinteger('position')->default(1)->unsigned();
            $table->boolean('visible')->default(1);
            $table->timestamps();
        });

        Schema::create('file_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('file_id')->unsigned();
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
