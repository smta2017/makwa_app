<?php

namespace App\Http\Controllers\Api\Customer;

use App\Customer;
use App\CustomerLocation;
use App\GlobalSetting;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class CustomerLocationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:customer-api', ['except' => [
            'updateCustomerNoLocation',
            'getAddressFromLatLong',
            'getCustomersLocations',
            'store'
        ]]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $customer = new CustomerLocation();
        $customer->customer_id = $request["CustomerId"];
        $customer->lat = $request->lat;
        $customer->longt = $request->longt;
        $customer->city = $request->city;
        $customer->area = $request->area;
        $customer->ip_address = $request->ip_address;
        $customer->app_ver = $request->app_ver;
        $customer->save();


        $sucsess_arr = array(
            'status' => 1,
            'message' => "Customer Location Added.",
        );
        return json_encode($sucsess_arr, JSON_NUMERIC_CHECK);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }



    //=========================================================

    public function getCustomersLocations()
    {
        // return "sameh";
        $customers_id = CustomerLocation::select("customer_id")->groupBy("customer_id")->get();
        // return $customers_id;
        $customers = Customer::with("CustomerLocations")->whereIn("id", $customers_id)->where('email', 'not like', '%smta%')->get();
        return $customers;
    }

    public function getAddressFromLatLong(Request $request)
    {
        $client = new Client();
        $full_url =  'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $request->point . '&key=AIzaSyAhtoUyHv6Q0Ch231O8LLPsJJ_QfSzh1rw';
        $res = $client->request('POST', $full_url, ['headers' => ['Content-Type' => 'application/json']]);
        $data_returned = json_decode($res->getBody(), true);
        return $data_returned["results"][0]["formatted_address"];
    }


    function updateCustomerNoLocation()
    {
        $customers_locations = CustomerLocation::whereNull("area")->get();
        $lats = '';
        foreach ($customers_locations as $customers_location) {
            $rrq = new Request();
            $rrq["point"] = $customers_location->lat . "," . $customers_location->longt;
            $addres = $this->getAddressFromLatLong($rrq);
            $customers_location->area = $addres;
            $customers_location->update();
        }
        return response()->json(["success" => 1], 200);
    }




    public function update_app(Request $request)
    {

        $app_ver = $request["app_ver"];
        $version_no = GlobalSetting::where("setting_name", "version_no")->first()["value"];

        if ($app_ver == $version_no) {

            $customer = Customer::find($request["CustomerId"]);
            $customer->update_app = null;
            $customer->update();

            $success_msg = array(
                "status" => 0,
                "message" => "No updates required",
            );
            return json_encode($success_msg);
        } else {
            $customer = Customer::find($request["CustomerId"]);
            $success_msg = array(
                "status" => 1,
                "update_app" => $customer["update_app"],
                "message" => "A new update is available - (2.0.4)",
            );
            return json_encode($success_msg);
        }
    }



    /**
     * Display the specified resource.
     *
     * 
     * 
     * @OA\post(
     *   path="/update_app",
     *   tags={"Makwa"},
     *   summary="Notify Us With app version",
     * security={{ "apiAuth": {} }},
     *   @OA\Response(
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
     *     @OA\Parameter(name=
     *   "app_ver",
     *     in="query",
     *     required=true,
     *         @OA\Schema(
     *           type="string",
     *           maxLength=10,
     *         ),
     *      style="form"
     *     ),
     * 
     * 
     * )
     */

    // check for update
    public function update_app_sssss(Request $request)
    {
        // $app_ver = $request["app_ver"];
        // $version_no = GlobalSetting::where("setting_name", "version_no")->first()["value"];
        // $customer_id = auth()->user()->id;
        // if ($app_ver == $version_no || '2.0.0') {

        //     $customer = Customer::find($customer_id);
        //     $customer->update_app = null;
        //     $customer->update();

        //     $success_msg = array(
        //         "status" => 0,
        //         "message" => "No updates required",
        //     );
        //     return json_encode($success_msg);
        // } else {
        //     $customer = Customer::find($customer_id);
        //     $success_msg = array(
        //         "status" => 1,
        //         "update_app" => $customer["update_app"],
        //         "message" => $customer["message_to_update"],
        //     );
        //     return json_encode($success_msg);
        // }
    }
}
