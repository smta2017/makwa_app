<?php

namespace App\Lib;

use App\Customer;
use App\Http\Helper\utilitiesHelper;
use App\Order;
use GuzzleHttp\Client;

class AppGainGetaway
{

    private static function _send($peyload)
    {
        $client = new Client();
        $res = $client->request('POST', 'https://notify.appgain.io/' . env("PROJECT_ID") . '/send', ['headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'appapikey' => env("APPAPIKEY")], 'body' => $peyload]);
        return $res->getBody();
    }
    public static function sendNotification(Order $order)
    {
        $subTotal = utilitiesHelper::orderSubTotal($order->id);
        $promoDiscount = utilitiesHelper::promotionCalculate($order->id, $subTotal);
        $vendorpromoDiscount = utilitiesHelper::vendorPromotionCalculate($order->id, $subTotal);
        $total_amount = utilitiesHelper::totalAmountCalculate($order->id, $subTotal, $promoDiscount + $vendorpromoDiscount);

        try {
            $payload = [
                "appPush" => [
                    "content-available" => 1,
                    "sound" => "",
                    "op" => "logpurchase-" . $total_amount,
                    "receivers" => [
                        ["userID" => strval($order->customer_id)],
                    ]
                ]
            ];
            $data = json_encode($payload);
            $sender =  json_decode(self::_send($data), true);
            return $sender['status'];
        } catch (\Throwable $th) {
            return;
        }
    }
}
