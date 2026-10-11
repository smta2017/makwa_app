<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\CustomerAddress::class, function (Faker $faker) {
    return [
        'customer_id' => factory(App\Customer::class),
        'type_id' => $faker->randomNumber(),
        'location_id' => factory(App\Location::class),
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'building_no' => $faker->word,
        'street_address' => $faker->word,
        'floor_no' => $faker->word,
        'apartment_no' => $faker->word,
        'additional_directions' => $faker->text,
        'is_default' => $faker->boolean,
        'affiliate_id' => $faker->randomNumber(),
        'address_type_id' => factory(App\addressType::class),
    ];
});
