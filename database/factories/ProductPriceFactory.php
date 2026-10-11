<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\ProductPrice::class, function (Faker $faker) {
    return [
        'product_id' => factory(App\Product::class),
        'service_id' => factory(App\Service::class),
        'price' => $faker->randomFloat(),
        'is_archive' => $faker->boolean,
    ];
});
