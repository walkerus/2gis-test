<?php

use App\Models\Firm;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FirmRubricSeeder extends Seeder
{
    public function run()
    {
        $rubricsIds = DB::table('rubrics')->pluck('id')->toArray();
        $faker = Factory::create();
        $firms = Firm::all();

        foreach ($firms->chunk(10_000) as $chunk) {
            $data = [];
            $chunk->each(function (Firm $firm) use ($faker, $rubricsIds, &$data) {
                for ($i = 0; $i < random_int(1, 3); $i++) {
                    $data[] = [
                        'firm_id' => $firm->id,
                        'rubric_id' => $faker->randomElement($rubricsIds),
                    ];
                }
            });
            DB::table('firm_rubric')->insert($data);
        }
    }
}
