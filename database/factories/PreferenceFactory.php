<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Preference::class, function (Faker $faker) {
    return [
        'product_id' => factory(App\Product::class),
        'type' => $faker->word,
        'default_value' => $faker->word,
    ];
});
