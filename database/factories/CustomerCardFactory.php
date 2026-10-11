<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\CustomerCard::class, function (Faker $faker) {
    return [
        'obj_id' => $faker->word,
        'token' => $faker->word,
        'masked_pan' => $faker->word,
        'merchant_id' => $faker->word,
        'card_subtype' => $faker->word,
        'email' => $faker->safeEmail,
        'order_id' => $faker->word,
        'user_added' => $faker->word,
        'customer_id' => $faker->randomNumber(),
        'is_default' => $faker->randomNumber(),
        'transaction_success' => $faker->word,
    ];
});
