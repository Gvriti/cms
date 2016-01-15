<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('type', 32);
            $table->string('admin_order_by', 32)->default('id');
            $table->string('admin_sort', 16)->default('desc');
            $table->boolean('admin_per_page')->default(20);
            $table->string('site_order_by', 32)->default('id');
            $table->string('site_sort', 16)->default('desc');
            $table->boolean('site_per_page')->default(10);
            $table->string('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('collections');
    }
}
