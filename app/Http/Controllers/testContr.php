<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\Helper\OrderHelperController;
use App\Order;
use App\User;
use App\Vendor;
use JWTFactory;
use JWTAuth;

class testContr extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:customer-api');
    }

    public function vtest($id)
    {
        return phpinfo();
        $order = Order::find($id);
        return OrderHelperController::orderRoutingVendor($order);
    }

    public function index()
    {
        // generate toket
        $customClaims = JWTFactory::customClaims(['exp' => '10000000000000', 'sub'   => 'sameh']);
        $payload = JWTFactory::make($customClaims);
        $token = JWTAuth::encode($payload, env('JWT_SECRET'));
        return $token;

        // decode token
        JWTAuth::setToken($token); //<-- set token and check
        $claim = JWTAuth::getPayload();
        return $claim;
    }


    public function rat()
    {
        $vendor = Vendor::first();
        $ratings = $vendor->averageDeliveryTimeRating();
        return $ratings;
    }

    public function setrt()
    {
        $user = User::first();
        $post = Vendor::first();

        $rating = $post->rating([
            'title' => 'This is a test title',
            'body' => 'And we will add some shit here',
            'customer_service_rating' => 5,
            'quality_rating' => 5,
            'friendly_rating' => 5,
            'pricing_rating' => 5,
            'delivery_time_rating' => 3,
            'rating' => 5,
            'recommend' => 'Yes',
            'approved' => true, // This is optional and defaults to false
        ], $user);
    }
}
