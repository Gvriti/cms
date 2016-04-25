<?php

class CmsUsersTableSeeder extends DatabaseSeeder
{
    /**
     * Run cms_users table seeder.
     *
     * @return void
     */
    public function run()
    {
        $currentDate = date('Y-m-d H:i:s');

        DB::table('cms_users')->truncate();

        DB::table('cms_users')->insert([
            [
                'email'      => 'dato@digitaldesign.ge',
                'firstname'  => 'David',
                'lastname'   => 'Gvritishvili',
                'role'       => 'admin',
                'active'     => 1,
                'password'   => '$2y$10$Z32s522b8mcksnaar80/k.sRjTvaxnLiUq87eOxtBi4ZbGYVpe/p2',
                'created_at' => $currentDate
            ]
        ]);

        DB::table('cms_settings')->truncate();

        DB::table('cms_settings')->insert([
            [
                'cms_user_id' => 1,
                'created_at'  => $currentDate
            ]
        ]);
    }
}
