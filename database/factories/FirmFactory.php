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

$factory->define(\App\Models\Firm::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\ru_RU\Company($faker));
    $faker->addProvider(new \Faker\Provider\ru_RU\PhoneNumber($faker));

    $phones = array_map(fn ($val) => $faker->unique()->phoneNumber, range(0, 3));

    return [
        'name' => $faker->unique()->company,
        'phones' => '{' . implode(',', $faker->randomElements($phones, random_int(1, 3))) . '}',
    ];
});
