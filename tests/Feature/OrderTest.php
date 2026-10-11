<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    // use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    //place order
    public function testStoreOrder()
    {
        $response = $this->json("POST", "/api/login", ['email' => 'smta0@yahoo.com', 'password' => '123456']);
        $response = $this->withHeaders([
            'Authorization' => $response["access_token"],
        ])->json('POST', '/api/place-order', ["fast_checkout" => 1, "customised_preference" => 0, "customer_id" => 6232, "address_id" => 1885, "method_of_payment" => "cash", "pickup_date" => "2020-10-25", "pickup_start" => "09:00:00", "pickup_end" => "12:00:00", "delivery_date" => "2020-10-27", "delivery_start" => "09:00:00", "delivery_end" => "12:00:00", "promo_code" => null, "products" => [], "preferences" => [], "services" => [], "selected_card" => "fbac7679875e83efd403624c2075f5b810226e7a417a60a661683a3c"]);
        $response->assertJson([
            'status' => 1,
        ])->assertStatus(200);
    }

    public function testStoreOrderWithPromo()
    {
        $response = $this->json("POST", "/api/login", ['email' => 'smta0@yahoo.com', 'password' => '123456']);
        $response = $this->withHeaders([
            'Authorization' => $response["access_token"],
        ])->json('POST', '/api/place-order', ["fast_checkout" => 1, "customised_preference" => 0, "customer_id" => 6232, "address_id" => 1885, "method_of_payment" => "cash", "pickup_date" => "2020-10-25", "pickup_start" => "09:00:00", "pickup_end" => "12:00:00", "delivery_date" => "2020-10-27", "delivery_start" => "09:00:00", "delivery_end" => "12:00:00", "promo_code" => "MiralxMakwa", "products" => [], "preferences" => [], "services" => [], "selected_card" => "fbac7679875e83efd403624c2075f5b810226e7a417a60a661683a3c"]);
        $response->assertJson([
            'status' => 1,
        ])->assertStatus(200);
    }

    // 1 validation
    public function testStoreOrderNoParameter()
    {
        $response = $this->json("POST", "/api/login", ['email' => 'smta0@yahoo.com', 'password' => '123456']);

        $response = $this->withHeaders([
            'Authorization' => $response["access_token"],
        ])->json('POST', '/api/place-order');
        $response->assertJson([
            "The customer id field is required.",
            "The address id field is required.",
            "The method of payment field is required.",
            "The pickup date field is required.",
            "The pickup start field is required.",
            "The pickup end field is required.",
            "The delivery date field is required.",
            "The delivery start field is required.",
            "The delivery end field is required.",
            "The customised preference field is required."
        ])->assertStatus(402);
    }

    // // 2 auth
    public function testNotifyLocationNoToken()
    {
        $response = $this->json('POST', '/api/place-order');
        $response->assertJson([
            "message" => "Invalid Token",
        ])->assertStatus(404);
    }



    // 1 ValidationData
    // 2 without token

}
