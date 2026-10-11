<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\VendorProduct::class, function (Faker $faker) {
    return [
        'vendor_id' => $faker->numberBetween(1,9),
        'product_id' => $faker->numberBetween(1,77),
        'active' => $faker->randomNumber(),
        'deleted_at' => $faker->dateTime(),
    ];
});
