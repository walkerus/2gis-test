<?php

use Illuminate\Database\Seeder;

class FirmsSeeder extends Seeder
{
    public function run()
    {
        $buildings = \Illuminate\Support\Facades\DB::table('buildings')->pluck('id')->toArray();
        $rubrics = \Illuminate\Support\Facades\DB::table('rubrics')->pluck('id')->toArray();
        $faker = \Faker\Factory::create();
        $data = [];

        for ($i = 0; $i < 10; $i++) {
            for ($j = 0; $j < 10_000; $j++) {
                $data[] = factory(\App\Models\Firm::class)->raw([
                    'building_id' => $faker->randomElement($buildings),
                    'rubrics' => '{' . implode(',', $faker->randomElements($rubrics, random_int(1, 3))) . '}',
                ]);
            }
            \Illuminate\Support\Facades\DB::table('firms')->insert($data);
            $data = [];
        }
    }
}
