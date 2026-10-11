<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Admin::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt($faker->password),
        'is_super' => $faker->boolean,
        'remember_token' => Str::random(10),
        'editing' => $faker->randomNumber(),
    ];
});
