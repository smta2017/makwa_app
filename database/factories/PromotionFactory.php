<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Promotion::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'type' => $faker->word,
        'discount_figure' => $faker->randomFloat(),
        'cap' => $faker->randomFloat(),
        'usage_limit' => $faker->randomNumber(),
        'min_order' => $faker->randomFloat(),
        'start_date' => $faker->date(),
        'end_date' => $faker->date(),
        'is_archive' => $faker->boolean,
        'title' => $faker->text,
        'message' => $faker->text,
        'show_in_app' => $faker->boolean,
        'promo_type' => $faker->randomNumber(),
        'applay_on_delivery' => $faker->randomNumber(),
    ];
});
