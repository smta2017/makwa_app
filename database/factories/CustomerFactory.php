<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Customer::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'phone' => $faker->phoneNumber,
        'country_code' => $faker->word,
        'verification_code' => $faker->word,
        'email' => $faker->safeEmail,
        'password' => bcrypt($faker->password),
        'cancellation_credit' => $faker->randomFloat(),
        'reschedule_credit' => $faker->randomFloat(),
        'is_verified' => $faker->boolean,
        'jwt_token' => $faker->word,
        'promo_code' => $faker->word,
        'referred_by' => $faker->randomNumber(),
        'is_archive' => $faker->randomNumber(),
        'is_signedOut' => $faker->boolean,
        'payment_customer_id' => $faker->word,
        'update_app' => $faker->randomNumber(),
        'message_to_update' => $faker->word,
        'is_company' => $faker->word,
        'disable_notification' => $faker->randomNumber(),
        'affiliate_id' => $faker->randomNumber(),
    ];
});
