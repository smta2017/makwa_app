<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\OrderServiceBag::class, function (Faker $faker) {
    return [
        'order_service_id' => factory(App\OrderService::class),
        'bag_id' => factory(App\Bag::class),
        'from_agent' => $faker->dateTime(),
        'from_vendor' => $faker->dateTime(),
        'to_customer' => $faker->dateTime(),
    ];
});
