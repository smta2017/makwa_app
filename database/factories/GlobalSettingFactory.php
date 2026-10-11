<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\GlobalSetting::class, function (Faker $faker) {
    return [
        'setting_name' => $faker->word,
        'value' => $faker->word,
    ];
});
