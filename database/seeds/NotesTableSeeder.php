<?php

class NotesTableSeeder extends DatabaseSeeder
{
    /**
     * Run notes table seeder.
     *
     * @return void
     */
    public function run()
    {
        $currentDate = date('Y-m-d H:i:s');

        DB::table('notes')->truncate();

        DB::table('notes')->insert([
            [
                'title'       => 'სათაური',
                'description' => 'მოკლე აღწერა',
                'content'     => "სათაური\nმოკლე აღწერა\nვრცელი ტექსტი...",
                'created_at'  => $currentDate
            ]
        ]);
    }
}
