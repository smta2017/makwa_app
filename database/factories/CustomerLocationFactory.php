<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\CustomerLocation::class, function (Faker $faker) {
    return [
        'customer_id' => factory(App\Customer::class),
        'lat' => $faker->latitude,
        'longt' => $faker->word,
        'city' => $faker->city,
        'area' => $faker->word,
        'ip_address' => $faker->word,
        'app_ver' => $faker->word,
        'full_address' => $faker->word,
    ];
});
