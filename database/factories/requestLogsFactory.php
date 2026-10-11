<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\requestLogs::class, function (Faker $faker) {
    return [
        'request' => $faker->text,
        'response' => $faker->text,
    ];
});
