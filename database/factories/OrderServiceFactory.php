<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\OrderService::class, function (Faker $faker) {
    return [
        'order_id' => factory(App\Order::class),
        'service_id' => factory(App\Service::class),
        'pickup_sorted' => $faker->word,
        'delivery_sorted' => $faker->word,
        'vendor_id' => factory(App\Vendor::class),
        'bag_count' => $faker->randomNumber(),
    ];
});
