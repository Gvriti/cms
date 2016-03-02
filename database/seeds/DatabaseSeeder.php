<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared('SET FOREIGN_KEY_CHECKS=0');

        $this->call('CmsUsersTableSeeder');

        $this->call('MenusTableSeeder');

        $this->call('NotesTableSeeder');

        DB::unprepared('SET FOREIGN_KEY_CHECKS=1');
    }
}
