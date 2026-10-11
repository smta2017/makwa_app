<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Order::class, function (Faker $faker) {
    return [
        'customer_id' => factory(App\Customer::class),
        'address_id' => factory(App\CustomerAddress::class),
        'rack_id' => $faker->randomNumber(),
        'total_price' => $faker->randomFloat(),
        'delivery_fee' => $faker->randomFloat(),
        'nextday_fee' => $faker->randomFloat(),
        'service_fee' => $faker->randomFloat(),
        'method_of_payment' => $faker->word,
        'selected_card' => $faker->word,
        'buyed' => $faker->word,
        'pickup_date' => $faker->date(),
        'pickup_start' => factory(App\TimeSlot::class),
        'pickup_end' => $faker->time(),
        'delivery_date' => $faker->date(),
        'delivery_start' => $faker->time(),
        'delivery_end' => $faker->time(),
        'pickup_user' => $faker->randomNumber(),
        'delivery_user' => $faker->randomNumber(),
        'status_id' => factory(App\OrderStatus::class),
        'is_fast' => $faker->boolean,
        'tax_amount' => $faker->randomFloat(),
        'discount_amount' => $faker->randomFloat(),
        'promotion_discount' => $faker->randomFloat(),
        'promotion_id' => factory(App\Promotion::class),
        'transaction_id' => $faker->word,
        'sub_total' => $faker->randomFloat(),
        'cancellation_fee' => $faker->randomFloat(),
        'reschedule_fee' => $faker->randomFloat(),
        'comments' => $faker->text,
        'rating' => $faker->randomFloat(),
        'is_rated' => $faker->boolean,
        'is_archive' => $faker->boolean,
        'canceled_at' => $faker->word,
        'vendor_id' => factory(App\Vendor::class),
        'confirm_prepare_route' => $faker->randomNumber(),
        'created_by' => $faker->word,
        'affiliate_id' => $faker->randomNumber(),
    ];
});
