<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\TimeSlot::class, function (Faker $faker) {
    return [
        'location_id' => factory(App\Location::class),
        'day' => $faker->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']),
        'start_time' => $faker->time(),
        'end_time' => $faker->time(),
        'type' => $faker->word,
        'is_archive' => $faker->boolean,
        'exp_at' => $faker->dateTime(),
        'exp_start' => $faker->dateTime(),
    ];
});
