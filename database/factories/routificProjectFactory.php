<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\routificProject::class, function (Faker $faker) {
    return [
        'project_id' => $faker->word,
        'project_name' => $faker->word,
    ];
});
