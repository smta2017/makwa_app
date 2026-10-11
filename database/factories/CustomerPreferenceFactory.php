<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\CustomerPreference::class, function (Faker $faker) {
    return [
        'preference' => $faker->word,
        'customer_id' => factory(App\Customer::class),
        'product_id' => factory(App\Product::class),
    ];
});
