<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Location::class, function (Faker $faker) {
    return [
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'city' => $faker->city,
        'country' => $faker->country,
        'zip' => $faker->postcode,
        'is_archive' => $faker->boolean,
    ];
});
