<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\VendorProductPrice::class, function (Faker $faker) {
    return [
        'vendor_id' => $faker->numberBetween(1,9),
        'product_id' => $faker->numberBetween(51,100),
        'service_id' => $faker->numberBetween(1,5),
        'price' => $faker->numberBetween(3,100),
    ];
});
