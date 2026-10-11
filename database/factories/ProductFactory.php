<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Product::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'name_ar' => $faker->word,
        'image' => $faker->word,
        'is_archive' => $faker->boolean,
    ];
});
