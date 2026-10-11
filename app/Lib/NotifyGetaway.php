<?php

namespace App\Lib;

use App\Customer;
use GuzzleHttp\Client;
use Illuminate\Notifications\Notification;

class NotifyGetaway
{

    private static function _send($peyload)
    {
        $client = new Client();
        $res = $client->request('POST', 'https://fcm.googleapis.com/fcm/send', ['headers' => ['Content-Type' => 'application/json', 'Authorization' => env("FIREBASE_TOKEN")], 'body' => $peyload]);
        return $res->getBody();
    }

    public static function sendNotification(Customer $customer, Notification $Notification, $body = "", $navTo = "", $id = "", $title = 'Makwa', $priority = 'high')
    {
        try {
            $payload = [
                "to" => $customer["firebase_id"],
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                    "priority" => $priority
                ],
                "data" => [
                    "navTo" => $navTo,
                    "id" => $id
                ]
            ];
            $data = json_encode($payload);
            $sender =  json_decode(self::_send($data), true);
            if ($sender['success']) {
                $customer->notify($Notification);
            }
        } catch (\Throwable $th) {
            return;
        }
    }
}
