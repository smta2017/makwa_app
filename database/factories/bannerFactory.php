<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\banner::class, function (Faker $faker) {
    return [
        'img' => $faker->word,
        'url_link' => $faker->word,
        'show_in_app' => $faker->randomNumber(),
        'expir_at' => $faker->date(),
        'dimension' => $faker->word,
    ];
});
