<?php

class MenusTableSeeder extends DatabaseSeeder
{
    /**
     * Run menus table seeder.
     *
     * @return void
     */
    public function run()
    {
        $currentDate = date('Y-m-d H:i:s');

        DB::table('menus')->truncate();

        DB::table('menus')->insert([
            [
                'main'        => 1,
                'title'       => 'მთავარი მენიუ',
                'description' => 'საიტის მთავარი გვერდების სია',
                'created_at'  => $currentDate
            ]
        ]);
    }
}
