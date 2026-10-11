<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\OrderProduct::class, function (Faker $faker) {
    return [
        'order_id' => factory(App\Order::class),
        'service_id' => $faker->randomNumber(),
        'product_id' => $faker->randomNumber(),
        'product_name' => $faker->word,
        'product_price' => $faker->randomFloat(),
        'product_discount' => $faker->randomFloat(),
        'preference' => $faker->word,
        'product_count' => $faker->randomNumber(),
        'pickup_sorted' => $faker->boolean,
        'delivery_sorted' => $faker->boolean,
        'is_added' => $faker->boolean,
        'product_price_id' => factory(App\ProductPrice::class),
        'order_service_id' => factory(App\OrderService::class),
    ];
});
