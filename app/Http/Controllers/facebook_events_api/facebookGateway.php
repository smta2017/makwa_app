<?php

namespace App\Http\Controllers\facebook_events_api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use GuzzleHttp\Client;

class facebookGateway extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:customer-api');
    }

    private static function _send($peyload)
    {
        $client = new Client();

        $full_url =  "https://graph.facebook.com/v8.0/" . env('FACEBOOK_PIXEL_ID') . "/events?access_token=" . env('FACEBOOK_PIXEL_ACCESS_TOKEN');
        $res = $client->request('POST', $full_url, ['headers' => ['Content-Type' => 'application/json'], 'body' => $peyload]);
        return $res->getBody();
    }


    public static function send_event($event_name = "empty", $custom_data = null)
    {
        $reqParamArray = [
            "data" => [
                [
                    "event_name" => $event_name,
                    "event_time" => Carbon::now()->timestamp,
                    "user_data" => [
                        "em" => "7b17fb0bd173f625b58636fb796407c22b3d16fc78302d79f0fd30c2fc2fc068",
                        "ph" => null
                    ],
                    "custom_data" => $custom_data
                ]
            ], "test_event_code" => "TEST54684"
        ];
        $data = json_encode($reqParamArray);
        $res =  json_decode(self::_send($data), true);
        return $res;
    }
}
