<?php

namespace App\Http\Controllers\Api\Helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Customer;
use App\Http\Requests\SMS\sendSMS;
use App\Lib\ResponseFormat;
use Illuminate\Http\JsonResponse;

class AppGainHelperController extends Controller
{

    private function _send($user_phone, $contry_code, $verification_code = null)
    {
        $VIA = 'sms';
        $ch = curl_init();
        if ($verification_code == null) {

            curl_setopt($ch, CURLOPT_URL, 'https://api.authy.com/protected/json/phones/verification/start');
            curl_setopt($ch, CURLOPT_POSTFIELDS, "via=$VIA&phone_number=$user_phone&country_code=$contry_code&locale='en'&code_length=6");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);

            $headers = array();
            $headers[] = 'X-Authy-Api-Key: V5j0gNwPJQuI97V0qI5MJTMOBhNVTu13';
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        } else {
            curl_setopt($ch, CURLOPT_URL, 'https://api.authy.com/protected/json/phones/verification/check');
            curl_setopt($ch, CURLOPT_POSTFIELDS, "phone_number=$user_phone&country_code=$contry_code&verification_code=$verification_code");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

            $headers = array();
            $headers[] = 'X-Authy-Api-Key: V5j0gNwPJQuI97V0qI5MJTMOBhNVTu13';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return json_decode($result);
    }

   
    public function sendNotification(Request $request)
    {
        // $customers = Customer::select('id')->where('phone', $request['phone'])->firstOrFail();

        $result = $this->_send($request['phone'], 2);

        if ($result->success) {
            return ResponseFormat::sucsess($result->message, 'Verification code sent.', JsonResponse::HTTP_OK);
        } else {
            return ResponseFormat::error($result->message, 'Unable to send verification code.', JsonResponse::HTTP_OK);
        }
    }

 
}
