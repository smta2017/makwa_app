<?php

namespace App\Http\Controllers\payment2;

use App\CustomerCard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\payment2\paymentGateway;
use App\Http\Helper\utilitiesHelper;
use App\Order;
use App\requestLogs;
use Auth;
use DB;

class paymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:customer-api', ['except' => ['withdrawal']]);
    }


    /**
     * 
     * 
     * @OA\post(
     *   path="/payment/add-new-card",
     *   tags={"Makwa"},
     *   summary="Adding New Card for Customer.",
     *   security={{ "apiAuth": {} }},
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
     * )
     *
     */
    public function addNewCard(Request $request)
    {
        $getAllTokens = paymentGateway::paymentKey(100, 1, auth()->user());
        return  $getAllTokens;
    }

    /**
     * 
     * 
     * @OA\post(
     *   path="/payment/customer-cards",
     *   tags={"Makwa"},
     *   summary="Get Customer Cards.",
     *   security={{ "apiAuth": {} }},
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
     * )
     *
     */

    public function customerCards()
    {
        $c_user = auth()->user();
        $customerCards =   CustomerCard::where('transaction_success', 1)->where('customer_id', $c_user->id)->get();
        $customerCards_arr = array();

        foreach ($customerCards as   $customerCard) {
            array_push($customerCards_arr, [
                "Token" => $customerCard->token,
                "TokenName" => "credit card",
                "TokenPostFix" => $customerCard->masked_pan,
                "IsDefault" => $customerCard->is_default,
                "Brand" => $customerCard->card_subtype

            ]);
        }

        $cards = [
            "status" => 1,
            "message" => "",
            "cards" => $customerCards_arr
        ];
        return  $cards;
    }


    public static function withdrawal($oid)
    {

        $orderChange = Order::find($oid);
        $products = $orderChange->OrderProducts;
      

        $subTotal = utilitiesHelper::orderSubTotal($oid);

        $promoDiscount = utilitiesHelper::promotionCalculate($oid, $subTotal);
        $vendorpromoDiscount = utilitiesHelper::vendorPromotionCalculate($oid, $subTotal);
        $total_amount = utilitiesHelper::totalAmountCalculate($oid, $subTotal, $promoDiscount+$vendorpromoDiscount );


        $Claims = [
            "customer" => $orderChange->Customer,
            "cardToken" => $orderChange->selected_card,
            "Amount" => $total_amount * 100,
            // "Amount" => 0.1 * 100,
        ];
        $requestLog = new requestLogs();
        $requestLog->request = json_encode($Claims);
        $requestLog->save();

        return paymentGateway::payByToken($Claims);
    }

    public static function withdrawalAmount($oid, $amount)
    {
        $orderChange = Order::find($oid);

        $Claims = [
            "customer" => $orderChange->Customer,
            "cardToken" => $orderChange->selected_card,
            "Amount" => $amount * 100,
        ];
        return paymentGateway::payByToken($Claims);
    }


    /**
     * 
     * 
     * @OA\post(
     *   path="/payment/set-card-default",
     *   tags={"Makwa"},
     *   summary="Set Customer Card To Default",
     *   security={{ "apiAuth": {} }},
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
     *  *     @OA\Parameter(name=
      "card_token", in="query",
     *  required=true,
     *     @OA\Schema(
     *       type="string",
     *       maxLength=100,
     *     )
     * ),
     * 
     * 
     * )
     *
     */

    public function setCardDefault(Request $request)
    {
        $request->validate([
            'card_token' => 'required',
        ]);

        $c_user = auth()->user();


        DB::select("update customer_cards set is_default = NULL where customer_id= " . $c_user->id);
        $ruery = "update customer_cards set is_default = 1 where customer_id= " . $c_user->id . " and token = '" . $request->card_token . "'";
        DB::select($ruery);


        $return_arr = array(
            'status' => 1,
            'message' => "Card Set To Default Successfully.",
        );

        return json_encode($return_arr, JSON_NUMERIC_CHECK);
    }

    /**
     * 
     * 
     * @OA\post(
     *   path="/payment/remove-card",
     *   tags={"Makwa"},
     *   summary="Remove Customer Card",
     *   security={{ "apiAuth": {} }},
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
     *  *     @OA\Parameter(name=
      "card_token", in="query",
     *  required=true,
     *     @OA\Schema(
     *       type="string",
     *       maxLength=100,
     *     )
     * ),
     * 
     * 
     * )
     *
     */
    public function removeCard(Request $request)
    {
        $request->validate([
            'card_token' => 'required',
        ]);

        $c_user = Auth::guard('customer-api')->user();

        $customerCards =   CustomerCard::where('customer_id', $c_user->id)->where('token', $request->card_token)->delete();

        if ($customerCards) {
            $return_arr = array(
                'status' => 1,
                'message' => "Card Removed Successfully.",
            );
        } else {
            $return_arr = array(
                'status' => 0,
                'message' => "Something Went Wrong.",
            );
        }
        return json_encode($return_arr, JSON_NUMERIC_CHECK);
    }
}
