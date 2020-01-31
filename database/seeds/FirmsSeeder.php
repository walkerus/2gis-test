<?php

use App\Models\Firm;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FirmsSeeder extends Seeder
{
    public function run()
    {
        $buildings = DB::table('buildings')->pluck('id')->toArray();
        $faker = Factory::create();
        $data = [];

        for ($i = 0; $i < 10; $i++) {
            for ($j = 0; $j < 10_000; $j++) {
                $data[] = factory(Firm::class)->raw([
                    'building_id' => $faker->randomElement($buildings),
                ]);
            }
            DB::table('firms')->insert($data);
            $data = [];
        }
    }
}
