<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Bag::class, function (Faker $faker) {
    return [
        'qrcode' => $faker->word,
        'service_id' => factory(App\Service::class),
        'status_id' => $faker->randomNumber(),
        'print_count' => $faker->randomNumber(),
    ];
});
