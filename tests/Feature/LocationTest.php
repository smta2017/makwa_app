<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LocationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetLocationCoveregArea()
    {
        $response = $this->json("POST", "/api/login", ['email' => 'smta0@yahoo.com', 'password' => '123456']);
        $response = $this->withHeaders([
            'Authorization' => $response["access_token"],
        ])->json('GET', '/api/get-location', ["latitude" => "30.0074", "longitude" => "31.4913"]);
        $response->assertJson([
            'status' => 1,
            'message' => 'Successfully retrieved nearby locations.',
        ])->assertStatus(200);
    }

    public function testGetLocationNoCoveregArea()
    {
        $response = $this->json("POST", "/api/login", ['email' => 'smta0@yahoo.com', 'password' => '123456']);
        $response = $this->withHeaders([
            'Authorization' => $response["access_token"],
        ])->json('GET', '/api/get-location', ["latitude" => "90.0074", "longitude" => "11.4913"]);
        $response->assertJson([
            'status' => 0,
        ])->assertStatus(200);
    }

    public function testGetLocationNoParameter()
    {
        $response = $this->json("POST", "/api/login", ['email' => 'smta0@yahoo.com', 'password' => '123456']);
        $response = $this->withHeaders([
            'Authorization' => $response["access_token"],
        ])->json('GET', '/api/get-location');
        $response->assertJson([
            'message' => "The given data was invalid.",
        ])->assertStatus(422);
    }

    public function testGetLocationsNoToken()
    {
        $response = $this->json('GET', '/api/get-location', ["latitude" => "30.0074", "longitude" => "31.4913"]);
        $response->assertJson([
            "message" => "Invalid Token",
        ])->assertStatus(404);
    }


    //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++



    // 3 success
    public function testNotifyLocation()
    {
        $response = $this->json("POST", "/api/login", ['email' => 'smta0@yahoo.com', 'password' => '123456']);
        $response = $this->withHeaders([
            'Authorization' => $response["access_token"],
        ])->json('POST', '/api/notify-location', ["customer_id" => "6232", "latitude" => "30.0074", "longitude" => "31.4913"]);
        $response->assertJson([
            'status' => 1,
        ])->assertStatus(200);
    }

    // 1 validation
    public function testNotifyLocationNoParameter()
    {
        $response = $this->json("POST", "/api/login", ['email' => 'smta0@yahoo.com', 'password' => '123456']);

        $response = $this->withHeaders([
            'Authorization' => $response["access_token"],
        ])->json('POST', '/api/notify-location');
        $response->assertJson([
            "The customer id field is required.",
            "The latitude field is required.",
            "The longitude field is required."
        ])->assertStatus(422);
    }

    // 2 auth
    public function testNotifyLocationNoToken()
    {
        // $response = $this->json("POST", "/api/login", ['email' => 'smta0@yahoo.com', 'password' => '123456']);
        $response = $this->json('POST', '/api/notify-location', ["customer_id" => "6232", "latitude" => "30.0074", "longitude" => "31.4913"]);
        $response->assertJson([
            "message" => "Invalid Token",
        ])->assertStatus(404);
    }
}
