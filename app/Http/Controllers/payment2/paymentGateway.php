<?php

namespace App\Http\Controllers\payment2;

use App\CustomerCard;
use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Auth;
use Illuminate\Support\Facades\Artisan;
use Log;
use function GuzzleHttp\json_decode;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class paymentGateway extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:customer-api', ['except' => ['callback_hook', 'github_webkhook']]);
    }

    //======================================================================================================================

    private static function _send($peyload_token, $route)
    {
        $client = new Client();
        $full_url =  env('GETWAY_URL2') . $route;
        // Log::debug($full_url . "+-+" . $peyload_token);
        $res = $client->request('POST', $full_url, ['headers' => ['Content-Type' => 'application/json'], 'body' => $peyload_token]);
        return $res->getBody();
    }


    public static function getToken()
    {
        // get our private token
        $route = "/auth/tokens";
        $reqParamArray = [
            'api_key' => env('API_KEY2')
        ];
        $data = json_encode($reqParamArray);
        $res =  json_decode(self::_send($data, $route), true);
        return [
            "token" => $res['token'],
            "merchant_id" => $res['profile']['id']
        ];
    }

    public static function Order_Regester($amount, $iframe)
    {
        // Order_Regestiration
        $route = "/ecommerce/orders";
        $auth_token =  self::getToken();

        $reqParamArray = [
            "auth_token" => $auth_token["token"],
            "delivery_needed" => "false",
            "merchant_id" => $auth_token["merchant_id"],
            "amount_cents" => $amount,
            "currency" => "EGP",
            "items" => []
        ];
        $data = json_encode($reqParamArray);
        $res =  json_decode(self::_send($data, $route), true);

        if ($iframe) {
            $CustomerCard = new CustomerCard();
            $CustomerCard->order_id = $res['id'];
            $CustomerCard->customer_id = Auth::guard('customer-api')->user()->id;
            $CustomerCard->email = Auth::guard('customer-api')->user()->email;
            $CustomerCard->save();
        }


        return [
            "token" => $auth_token["token"],
            "order_id" => $res['id']
        ];
    }

    public static function paymentKey($amount, $customer = null, $iframe = 1)
    {
        // paymentKey

        $Order_Regester =  self::Order_Regester($amount, $iframe);

        $route = "/acceptance/payment_keys";

        $integration_id = env('INTEGRATION_ID');
        if ($iframe == 0) {
            $integration_id = env('INTEGRATION_ID_MOTO');
        }
        $reqParamArray = [
            "auth_token" => $Order_Regester["token"],
            "amount_cents" => $amount,
            "expiration" => 3600,
            "order_id" => $Order_Regester["order_id"],
            "billing_data" => [
                "first_name" => $customer->first_name,
                "last_name" => ($customer->last_name != '') ? $customer->last_name : "last_name",
                "email" => $customer->email,
                "phone_number" => $customer->phone,
                "apartment" => "NA",
                "floor" => "NA",
                "street" => "NA",
                "building" => "NA",
                "shipping_method" => "NA",
                "postal_code" => "NA",
                "city" => "NA",
                "country" => "NA",
                "state" => "NA"
            ],
            "currency" => "EGP",
            "integration_id" => $integration_id,
            "lock_order_when_paid" => "false"
        ];
        $data = json_encode($reqParamArray);
        $res =  json_decode(self::_send($data, $route), true);

        $iframe_id = 0;
        if ($iframe == 1) {
            $iframe_id = 39405;
        } elseif ($iframe == 2) {
            $iframe_id = 39406;
        } elseif ($iframe == 3) {
            $iframe_id = 39407;
        }



        return [
            "token" => $res["token"],
            "url" => "https://accept.paymobsolutions.com/api/acceptance/iframes/" . $iframe_id . "?payment_token=" . $res["token"]
        ];
    }

    public static function payByToken($request)
    {
        $customer = $request["customer"];
        $Amount = $request["Amount"];
        $cardToken = $request["cardToken"];

        $payment_key = self::paymentKey($Amount, 0, $customer);

        // get our private token
        $route = "/acceptance/payments/pay";
        $reqParamArray = [
            "source" => [
                "identifier" => $cardToken,
                "subtype" => "TOKEN"
            ],
            "payment_token" => $payment_key["token"]
        ];
        $data = json_encode($reqParamArray);
        $res =  json_decode(self::_send($data, $route), true);
        return $res;
        return [
            "token" => $res['token'],
            "merchant_id" => $res['profile']['id']
        ];
    }



    public static function callback_hook(Request $request)
    {
        $hmacSecret = config('services.paymob.hmac_secret') ?? env('PAYMOB_HMAC_SECRET');
        if (!empty($hmacSecret)) {
            $hmac = $request->query('hmac') ?? $request->input('hmac');
            if (empty($hmac)) {
                \Log::warning('Paymob callback rejected: missing hmac query parameter.');
                return response()->json(['error' => 'Missing HMAC signature'], 400);
            }

            $obj = $request->input('obj', []);
            if (is_array($obj) && !empty($obj)) {
                $concatenated = 
                    ($obj['amount_cents'] ?? '') .
                    ($obj['created_at'] ?? '') .
                    ($obj['currency'] ?? '') .
                    (($obj['error_occured'] ?? false) === true ? 'true' : 'false') .
                    (($obj['has_parent_transaction'] ?? false) === true ? 'true' : 'false') .
                    ($obj['id'] ?? '') .
                    ($obj['integration_id'] ?? '') .
                    (($obj['is_3d_secure'] ?? false) === true ? 'true' : 'false') .
                    (($obj['is_auth'] ?? false) === true ? 'true' : 'false') .
                    (($obj['is_capture'] ?? false) === true ? 'true' : 'false') .
                    (($obj['is_refunded'] ?? false) === true ? 'true' : 'false') .
                    (($obj['is_standalone_payment'] ?? false) === true ? 'true' : 'false') .
                    ($obj['order']['id'] ?? '') .
                    ($obj['owner'] ?? '') .
                    (($obj['pending'] ?? false) === true ? 'true' : 'false') .
                    ($obj['source_data']['pan'] ?? '') .
                    ($obj['source_data']['sub_type'] ?? '') .
                    ($obj['source_data']['type'] ?? '') .
                    (($obj['success'] ?? false) === true ? 'true' : 'false');

                $calculatedHmac = hash_hmac('sha512', $concatenated, $hmacSecret);
                if (!hash_equals($calculatedHmac, $hmac)) {
                    \Log::warning('Paymob callback rejected: invalid HMAC signature.');
                    return response()->json(['error' => 'Invalid HMAC signature'], 401);
                }
            }
        }

        $type = $request->input('type');
        $obj = $request->input('obj', []);

        if ($type == "TRANSACTION") {
            $orderId = $obj["order"]['id'] ?? null;
            if ($orderId) {
                $CustomerCard = CustomerCard::where('order_id', $orderId)->first();
                if ($CustomerCard) {
                    $isSuccess = ($obj['success'] ?? false) == "true" || ($obj['success'] ?? false) === true;
                    $CustomerCard->transaction_success = $isSuccess ? 1 : 0;
                    $CustomerCard->save();
                }
            }
        } else if ($type == "TOKEN") {
            $orderId = $obj['order_id'] ?? null;
            if ($orderId) {
                $CustomerCard = CustomerCard::where('order_id', $orderId)->first();
                if ($CustomerCard) {
                    $CustomerCard->obj_id = $obj['id'] ?? null;
                    $CustomerCard->token = $obj['token'] ?? null;
                    $CustomerCard->masked_pan = isset($obj['masked_pan']) ? substr($obj['masked_pan'], -4) : null;
                    $CustomerCard->merchant_id = $obj['merchant_id'] ?? null;
                    $CustomerCard->card_subtype = isset($obj['card_subtype']) ? strtolower($obj['card_subtype']) : null;
                    $CustomerCard->email = $obj['email'] ?? null;
                    $CustomerCard->user_added = $obj['user_added'] ?? null;
                    $CustomerCard->save();
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    public static function github_webkhook(Request $request)
    {
        return response()->json(['status' => 'ignored'], 200);
    }
}
