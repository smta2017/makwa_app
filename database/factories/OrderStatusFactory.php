<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\OrderStatus::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'is_archive' => $faker->boolean,
    ];
});
