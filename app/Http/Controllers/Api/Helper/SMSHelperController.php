<?php

namespace App\Http\Controllers\Api\Helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Customer;
use App\Http\Requests\SMS\sendSMS;
use App\Lib\ResponseFormat;
use Illuminate\Http\JsonResponse;

class SMSHelperController extends Controller
{

    private function _sendSMS($user_phone, $contry_code, $verification_code = null)
    {
        $VIA = 'sms';
        $ch = curl_init();
        if ($verification_code == null) {

            curl_setopt($ch, CURLOPT_URL, 'https://api.authy.com/protected/json/phones/verification/start');
            curl_setopt($ch, CURLOPT_POSTFIELDS, "via=$VIA&phone_number=$user_phone&country_code=$contry_code&locale='en'&code_length=6");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);

            $authyKey = config('services.authy.key', env('AUTHY_API_KEY'));
            $headers = array();
            $headers[] = 'X-Authy-Api-Key: ' . $authyKey;
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        } else {
            curl_setopt($ch, CURLOPT_URL, 'https://api.authy.com/protected/json/phones/verification/check');
            curl_setopt($ch, CURLOPT_POSTFIELDS, "phone_number=$user_phone&country_code=$contry_code&verification_code=$verification_code");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

            $authyKey = config('services.authy.key', env('AUTHY_API_KEY'));
            $headers = array();
            $headers[] = 'X-Authy-Api-Key: ' . $authyKey;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return json_decode($result);
    }

    /**
     *
     * @OA\post(
     *   path="/sms/send-pin",
     *   tags={"Makwa"},
     *   summary="Send SMS Verification Pin",
     *
     *    @OA\Response(
     *     response=200,
     *     description="A list with products", @OA\JsonContent()
     *   ),
     *   @OA\Response(
     *         response="400",
     *         description="Invalid tag value", 
     *     ),
     *   @OA\Response(
     *         response="404",
     *         description="not found", 
     *     ),
     *   @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity", 
     *     ),
     * 
     * 
     *   @OA\Parameter(
     *  name="phone",
     *     in="query",
     *     required=true,
     *         @OA\Schema(
     *           type="string",
     *           maxLength=100,
     * 
     *         )
     *     ),
     * 
     *    @OA\Parameter(
     *  name="country_code",
     *     in="query",
     *         @OA\Schema(
     *           type="string",
     *           maxLength=100,
     * 
     *         )
     *     ),
     *  
     * 
     * 
     * )
     */
    public function sendSMSVerificationPin(sendSMS $request)
    {
        // $customers = Customer::select('id')->where('phone', $request['phone'])->firstOrFail();

        $result =  $this->_sendSMS($request['phone'], 2);

        if ($result->success) {
            return ResponseFormat::sucsess($result->message, 'Verification code sent.', JsonResponse::HTTP_OK);
        } else {
            return ResponseFormat::error($result->message, 'Unable to send verification code.', JsonResponse::HTTP_OK);
        }
    }


    /**
     *
     * @OA\post(
     *   path="/sms/confirm-pin",
     *   tags={"Makwa"},
     *   summary="confirm SMS Verification Pin",
     *
     *    @OA\Response(
     *     response=200,
     *     description="A list with products", @OA\JsonContent()
     *   ),
     *   @OA\Response(
     *         response="400",
     *         description="Invalid tag value", 
     *     ),
     *   @OA\Response(
     *         response="404",
     *         description="not found", 
     *     ),
     *   @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity", 
     *     ),
     * 
     * 
     *   @OA\Parameter(
     *  name="phone",
     *     in="query",
     *     required=true,
     *         @OA\Schema(
     *           type="string",
     *           maxLength=100,
     * 
     *         )
     *     ),
     * 
     *    @OA\Parameter(
     *  name="country_code",
     *     in="query",
     *         @OA\Schema(
     *           type="string",
     *           maxLength=100,
     * 
     *         )
     *     ),
     * 
     *    @OA\Parameter(
     *  name="verification_code",
     *     in="query",
     *     required=true,
     *         @OA\Schema(
     *           type="string",
     *           maxLength=100,
     * 
     *         )
     *     ),
     *  
     *     @OA\Parameter(
     *   name="customer_id",
     *     in="query",
     *     required=true,
     *         @OA\Schema(
     *           type="integer",
     *           maxLength=100,
     * 
     *         )
     *     ),
     * 
     * )
     */
    public function confirmSMSVerficationPin(sendSMS $request)
    {
        $result =  $this->_sendSMS($request['phone'], 2, $request['verification_code']);



        if ($result->success) {
            $customer =   Customer::findOrFail($request['customer_id']);
            $customer->is_verified = 1;
            $customer->save();
            return ResponseFormat::sucsess($result->message, 'Verification code Confirmed.', JsonResponse::HTTP_OK);
        } else {
            return ResponseFormat::error($result->message, 'Unable to confirm verification code.', JsonResponse::HTTP_OK);
        }
    }



    public function resendSMSVerificationPin(Request $request)
    {
        $USER_PHONE = $request['phone'];
        $COUNTRY_CODE = $request['country_code'];

        if (!empty($request['phone']) && isset($request['phone']) && !empty($request['country_code']) && isset($request['country_code'])) {
            $customers = Customer::select('id')
                ->where('phone', $request['phone'])
                ->where('country_code', $request['country_code'])
                ->first();
            if (!empty($customers)) {
                $failure_array = array(
                    "status" => 0,
                    "message" => "This phone number is already been used."
                );
                return response()->json($failure_array, 400);
            }
        }

        $VIA = 'sms';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.authy.com/protected/json/phones/verification/start');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "via=$VIA&phone_number=$USER_PHONE&country_code=$COUNTRY_CODE&locale='en'&code_length=6");
        curl_setopt($ch, CURLOPT_POST, 1);

        $authyKey = config('services.authy.key', env('AUTHY_API_KEY'));
        $headers = array();
        $headers[] = 'X-Authy-Api-Key: ' . $authyKey;
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        curl_close($ch);
        $result1 = json_decode($result);

        if (!empty($result1) && isset($result1->success) && $result1->success) {
            $send_code = array(
                "status" => 1,
                "message" => "Verification code sent to your phone number."
            );
            return response()->json($send_code, 200);
        } else {
            $send_code = array(
                "status" => 0,
                "message" => "Unable to send verification code. Please try again."
            );
            return response()->json($send_code, 400);
        }
    }

    public function checkSMSVerficationPin(Request $request)
    {
        $USER_PHONE = $request['phone'];
        $COUNTRY_CODE = $request['country_code'];
        $VIA = 'sms';
        $VERIFICATION_CODE = $request['verification_code'];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.authy.com/protected/json/phones/verification/check');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "phone_number=$USER_PHONE&country_code=$COUNTRY_CODE&verification_code=$VERIFICATION_CODE");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $authyKey = config('services.authy.key', env('AUTHY_API_KEY'));
        $headers = array();
        $headers[] = 'X-Authy-Api-Key: ' . $authyKey;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result);
        if (!empty($result) && isset($result->success) && $result->success) {
            $verify_code = array(
                "status" => 1,
                "message" => "Verification code verified successfully.",
            );

            return response()->json($verify_code, 200);
        } else {
            $verify_code = array(
                "status" => 0,
                "message" => "Verification code expired/incorrect. Please try resending verification code."
            );
            return response()->json($verify_code, 400);
        }
    }
}
