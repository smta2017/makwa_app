<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Vendor::class, function (Faker $faker) {
    return [
        'icon' => $faker->word,
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt($faker->password),
        'country_code' => $faker->word,
        'phone' => $faker->phoneNumber,
        'city' => $faker->city,
        'country' => $faker->country,
        'address' => $faker->text,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'is_archive' => $faker->boolean,
        'joined_at' => $faker->date(),
        'delivery_fee' => $faker->randomFloat(),
        'max_item' => $faker->randomNumber(),
        'max_orders' => $faker->randomNumber(),
        'location_id' => $faker->randomNumber(),
        'available' => $faker->randomNumber(),
        'min_order_price' => $faker->word,
        'active' => $faker->randomNumber(),
        'head' => $faker->randomNumber(),
    ];
});
