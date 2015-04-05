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
                'role'       => 0,
                'active'     => 1,
                'password'   => '$2a$10$EPz1yOC.C5aLDXl3o3E0p.ybr1wBJAyWWkCETPRPM/mJ1fL03aY8K',
                'created_at' => $currentDate
            ],
            [
                'email'      => 'email@example.com',
                'firstname'  => 'სახელი',
                'lastname'   => 'გვარი',
                'role'       => 1,
                'active'     => 1,
                'password'   => '$2a$10$We95h4v/f2WFty/ls6Z6aOMskhmVR/70Pc1woKRufRx5jX6q3J0Sy',
                'created_at' => $currentDate
            ]
        ]);
    }
}
