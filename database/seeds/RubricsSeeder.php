<?php

use Illuminate\Database\Seeder;

class RubricsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        $createRubrics = function (int $depth, array $ancestors) use (&$createRubrics, $faker) {
            if ($depth === 0) {
                return;
            }

            $childrenCount = empty($ancestors) ? 5 : random_int(0, 5);

            for ($i = 0; $i < $childrenCount; $i++) {
                $rubric = new \App\Models\Rubric();
                $rubric->name = $faker->unique()->lexify('??????');
                $rubric->ancestors = '{' . implode(',', $ancestors) . '}';
                $rubric->save();

                $newAncestors = $ancestors;
                $newAncestors[] = $rubric->id;
                $createRubrics($depth - 1, $newAncestors);
            }
        };

        $createRubrics(5, []);
    }
}
