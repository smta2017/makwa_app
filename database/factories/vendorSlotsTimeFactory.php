<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\VendorSlotsTime::class, function (Faker $faker) {
    return [
        'vendor_id' => $faker->randomNumber(),
        'time_slots_id' => $faker->randomNumber(),
    ];
});
