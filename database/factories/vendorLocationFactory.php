<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\vendorLocation::class, function (Faker $faker) {
    return [
        'vendor_id' => factory(App\Vendor::class),
        'location_id' => factory(App\Location::class),
        'available' => $faker->randomNumber(),
        'head' => $faker->randomNumber(),
    ];
});
