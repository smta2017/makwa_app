<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Service::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'color' => $faker->word,
        'is_archive' => $faker->boolean,
    ];
});
