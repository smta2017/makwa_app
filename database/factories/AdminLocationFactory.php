<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\AdminLocation::class, function (Faker $faker) {
    return [
        'admin_id' => factory(App\Admin::class),
        'location_id' => factory(App\Location::class),
        'archive' => $faker->randomNumber(),
    ];
});
