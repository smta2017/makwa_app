<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\CustomerBag::class, function (Faker $faker) {
    return [
        'customer_id' => factory(App\Customer::class),
        'bag_id' => factory(App\Bag::class),
    ];
});
