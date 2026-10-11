<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BannerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function testGetBanner()
    {
        $response = $this->json("POST", "/api/login", ['email' => 'smta0@yahoo.com', 'password' => '123456']);

        $response = $this->withHeaders([
            'Authorization' => $response["access_token"],
        ])->json('GET', '/api/get-bunners');

        $response->assertJson([
            'success' => 1,
        ])->assertStatus(200);
    }

    public function testGetBannerNoToken()
    {
        $response = $this->json('GET', '/api/get-bunners');
        $response->assertJson([
            "message" => "Invalid Token",
        ])->assertStatus(404);
    }
}
