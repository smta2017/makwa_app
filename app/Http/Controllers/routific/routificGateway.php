<?php

namespace App\Http\Controllers\routific;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;


use function GuzzleHttp\json_decode;

class routificGateway extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:customer-api');
    }

    //======================================================================================================================

    private static function _send($peyload_token, $route)
    {
        $client = new Client();
        $full_url = $route;
        $token = config('services.routific.bearer_token', env('ROUTIFIC_BEARER_TOKEN'));
        $res = $client->request('POST', $full_url, ['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token], 'body' => $peyload_token]);
        return $res->getBody();
    }


    public static function create_project($drivers, $stops)
    {
        // get our private token
        $route = "https://product-api.routific.com/v1.0/project";
        $cdat = Carbon::now()->format('Y-m-d');
        $reqParamArray = [
            "name" => Carbon::now()->format('D d F') . "  Auto",
            "date" => $cdat,
            "drivers" => $drivers,
            "stops" => $stops,
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
        $data = json_encode($reqParamArray);
        $res =  json_decode(self::_send($data, $route), true);
        return $res;
    }


    public static function update_project($project_id, $stop)
    {
        // get our private token
        $route = "https://product-api.routific.com/v0.1/project/$project_id/stops/";

        $reqParamArray2 =[$stop];

        //   return $route;
        $reqParamArray = [[
            "name" => "Jane Doe",
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
            "custom_notes" => ["COD" => "CCOODD"]
        ]];

        $data = json_encode($reqParamArray2);
        $res =  json_decode(self::_send($data, $route), true);
        return $res;
    }

    public static function callback_hook(Request $request)
    {
    }
}
