<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Holiday::class, function (Faker $faker) {
    return [
        'the_date' => $faker->date(),
    ];
});
