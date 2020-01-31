<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(BuildingsSeeder::class);
         $this->call(RubricsSeeder::class);
         $this->call(FirmsSeeder::class);
         $this->call(FirmRubricSeeder::class);
    }
}
