<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\OrderFastPreference::class, function (Faker $faker) {
    return [
        'order_id' => factory(App\Order::class),
        'product_id' => factory(App\Product::class),
        'preference' => $faker->word,
        'name' => $faker->name,
    ];
});
