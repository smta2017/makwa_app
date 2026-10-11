<?php

namespace App\Http\Controllers;

use App\OrderProduct;
use App\OrderBag;
use Illuminate\Http\Request;

// require 'vendor/autoload.php';
use GuzzleHttpClient;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
 

    public function changePassword()
    {
        return view("admin.auth.passwords.change");
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $orderc = new \App\Http\Controllers\Api\Order\OrderController();

        $orderc->changeOrderStatus();

        return view('admin.admin');
    }

    public function routific()
    {

        $url = 'https://product-api.routific.com/v1.0/project';
        $client = new \GuzzleHttp\Client([
            'base_uri' => $url,
        ]);


        $payload = [
            "name" => "Vancouver Delivery1_" . date('h:i'),
            "date" => "2020-10-01",
            "drivers" => [
                [
                    "name" => "John Doe",
                    "start_location" => [
                        "address" => "555 west hastings, vancouver bc, canada",
                        "coords" => [
                            "lat" => 49.2847001,
                            "lng" => -123.1141236
                        ]
                    ],
                    "end_location" => [
                        "address" => "555 west hastings, vancouver bc, canada",
                        "coords" => [
                            "lat" => 49.2847001,
                            "lng" => -123.1141236
                        ]
                    ],
                    "shift_start" => "09:00",
                    "shift_end" => "17:00",
                    "phone_number" => "+201274200778",
                    "speed" => 1,
                    "capacity" => 10,
                    "types" => ["a", "b"],
                    "break" => [
                        "start" => "12:00",
                        "end" => "13:00"
                    ]
                ],
                [
                    "name" => "John Doe2",
                    "start_location" => [
                        "address" => "555 west hastings, vancouver bc, canada",
                        "coords" => [
                            "lat" => 49.2847001,
                            "lng" => -123.1141236
                        ]
                    ],
                    "end_location" => [
                        "address" => "555 west hastings, vancouver bc, canada",
                        "coords" => [
                            "lat" => 49.2847001,
                            "lng" => -123.1141236
                        ]
                    ],
                    "shift_start" => "09:00",
                    "shift_end" => "17:00",
                    "phone_number" => "+201274200778",
                    "speed" => 1,
                    "capacity" => 10,
                    "types" => ["a", "b"],
                    "break" => [
                        "start" => "12:00",
                        "end" => "13:00"
                    ]
                ]
            ],
            "stops" => [
                [
                    "name" => "ali",
                    "location" => [
                        "address" => "2148 Main St, Vancouver, BC V5T 3C5",
                        "coords" => [
                            "lat" => 49.2657634,
                            "lng" => -123.1004459
                        ]
                    ],
                    "start" => "10:00",
                    "end" => "11:00",
                    "duration" => 20,
                    "types" => ["a"],
                    "load" => 1,
                    "priority" => true,
                    "phone_number" => "+16046204589",
                    "email" => "jane@doe.com",
                    "notes" => "Press 304 at buzzer.",
                    // "custom_notes"=> []
                ],
                [
                    "name" => "sameh test",
                    "location" => [
                        "address" => "2148 Main St, Vancouver, BC V5T 3C5",
                        "coords" => [
                            "lat" => 49.0657634,
                            "lng" => -123.0004459
                        ]
                    ],
                    "start" => "10:00",
                    "end" => "11:00",
                    "duration" => 20,
                    "types" => ["a"],
                    "load" => 1,
                    "priority" => true,
                    "phone_number" => "+201274200778",
                    "email" => "jane@doe.com",
                    "notes" => "Press 304 at buzzer.",
                    // "custom_notes"=> [""]
                ]
            ],
            "settings" => [
                "max_stop_lateness" => 320,
                "max_driver_overtime" => 320,
                "shortest_distance" => true,
                "traffic" => 1.4,
                "strict_start" => true,
                "auto_balance" => true,
                "default_load" => 1,
                "default_duration" => 10
            ]
        ];


        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiI1ZTJmNjhlN2FmOTA2NTJiNzQxOWYxOTgiLCJpYXQiOjE1ODMzMTc0NzF9.e7Z7vDgEEtUEiH1u-G5FBP97vj-yvfz7Gax-mGVtujQ';

        $response = $client->post($url, [
            'json' => $payload,
            'headers' => [
                "Content-type" => "application/json",
                "Authorization" => "bearer " . $token
            ]
        ]);


        print_r($response);
    }


    public function routific2()
    {

        // Step 1: Prepare http client and set routific base url

        $url = 'https://api.routific.com/v1/pdp';
        $client = new \GuzzleHttp\Client([
            'base_uri' => $url,
        ]);

        // Step 2: Prepare visits
        $order1 = array(
            "location" => array(
                "name" => "6800 Cambie",
                "lat" => 49.227107,
                "lng" => -123.1163085
            )
        );
        $order2 = array(
            "location" => array(
                "name" => "3780 Arbutus",
                "lat" => 49.2474624,
                "lng" => -123.1532338
            )
        );
        $order3 = array(
            "location" => array(
                "name" => "800 Robson",
                "lat" => 49.2819229,
                "lng" => -123.1211844
            )
        );
        $visits = array(
            "order_1" => $order1,
            "order_2" => $order2,
            "order_3" => $order3
        );

        // Step 3: Prepare vehicles
        $vehicle1 = array(
            "start_location" => array(
                "id" => "depot",
                "name" => "800 Kingsway",
                "lat" => 49.2553636,
                "lng" => -123.0873365
            )
        );

        $vehicle2 = array(
            "start_location" => array(
                "id" => "depot",
                "name" => "800 Kingsway",
                "lat" => 49.2553636,
                "lng" => -123.0873365
            )
        );

        $vehicles = array(
            "vehicle_1" => $vehicle1,
            "vehicle_2" => $vehicle2,
        );

        // Step 4: Prepare data payload
        $payload = array(
            "visits" => $visits,
            "fleet" => $vehicles
        );

        // Step 5: Send request

        // This is your demo token
        // This is your demo token
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJfaWQiOiI1MzEzZDZiYTNiMDBkMzA4MDA2ZTliOGEiLCJpYXQiOjEzOTM4MDkwODJ9.PR5qTHsqPogeIIe0NyH2oheaGR-SJXDsxPTcUQNq90E';

        $response = $client->post($url, [
            'json' => $payload,
            'headers' => [
                "Content-type" => "application/json",
                "Authorization" => "bearer " . $token
            ]
        ]);
        echo $response->getBody();
    }

    public function qrCodeIndex()
    {
        return view('admin.qrcode');
    }

    public function addVendorTimeSlot(Request $request)
    {
        return $request;
    }
}
