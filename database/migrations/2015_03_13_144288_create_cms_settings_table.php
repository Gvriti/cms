<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_settings', function (Blueprint $table) {
            $table->tinyInteger('id', true);
            $table->string('sidebar_position', 16)->default('fixed');
            $table->string('sidebar_direction', 16)->default('left-sidebar');
            $table->boolean('horizontal_menu')->default(0);
            $table->string('horizontal_menu_minimal', 16)->nullable();
            $table->string('horizontal_menu_click', 16)->nullable();
            $table->string('skin_sidebar', 64)->nullable();
            $table->string('skin_user_menu', 64)->nullable();
            $table->string('skin_horizontal', 64)->nullable();
            $table->string('skin_login', 64)->nullable();
            $table->string('layout_boxed', 16)->nullable();
            $table->string('alert_position', 32)->default('top-right');
            $table->string('ajax_form', 16)->default('ajax-form');
            $table->string('lockscreen')->default('600000');
            $table->timestamps();
        });

        // Insert default row.
        // Seeding in migration, because of triggers constraint.
        DB::table('cms_settings')->insert([[]]);

        // Create triggers
        DB::unprepared(
'CREATE TRIGGER `cms_settings_insert_not_allowed` BEFORE INSERT ON `cms_settings`
FOR EACH ROW BEGIN
    SIGNAL SQLSTATE "45000"
    SET MESSAGE_TEXT = "insert not allowed";
END'
);
        DB::unprepared(
'CREATE TRIGGER `cms_settings_delete_not_allowed` BEFORE DELETE ON `cms_settings`
FOR EACH ROW BEGIN
    SIGNAL SQLSTATE "45000"
    SET MESSAGE_TEXT = "delete not allowed";
END'
);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cms_settings');
    }
}
