<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\VendorService::class, function (Faker $faker) {
    return [
        'vendor_id' => $faker->randomNumber(),
        'service_id' => $faker->randomNumber(),
        'active' => $faker->randomNumber(),
        'deleted_at' => $faker->dateTime(),
    ];
});
