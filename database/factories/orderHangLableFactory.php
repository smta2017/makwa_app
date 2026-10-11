<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\orderHangLable::class, function (Faker $faker) {
    return [
        'order_id' => factory(App\Order::class),
        'lable_code' => $faker->word,
        'status' => $faker->randomNumber(),
        'from_vendor' => $faker->dateTime(),
        'to_customer' => $faker->dateTime(),
    ];
});
