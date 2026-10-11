<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\NotifyLocation::class, function (Faker $faker) {
    return [
        'customer_id' => factory(App\Customer::class),
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'status' => $faker->boolean,
        'address' => $faker->word,
        'area' => $faker->word,
    ];
});
