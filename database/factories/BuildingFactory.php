<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(\App\Models\Building::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\ru_RU\Address($faker));

    return [
        'address' => $faker->unique()->streetAddress,
        'latitude' => $faker->randomFloat(null, 54.5, 55.0),
        'longitude' => $faker->randomFloat(null, 85.5, 86.0),
    ];
});
