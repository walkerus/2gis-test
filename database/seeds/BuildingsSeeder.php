<?php

use Illuminate\Database\Seeder;

class BuildingsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Building::class, 10_000)->create();
    }
}
