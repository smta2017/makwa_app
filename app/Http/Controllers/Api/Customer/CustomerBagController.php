<?php

namespace App\Http\Controllers\Api\Customer;

use App\Bag;
use App\CustomerBag;
use App\Http\Controllers\Controller;
use Dotenv\Regex\Success;
use Illuminate\Http\Request;

class CustomerBagController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customer_id = $request->customer_id;
        $service_id = $request->service_id;

        $maxBagId = (int) bag::where('id', bag::max('id'))->first('qrcode')['qrcode'] + 1;
        $bag = new Bag();
        $bag->qrcode = sprintf('%1$05d', $maxBagId);
        $bag->service_id = $service_id;
        $bag->customer_id = $customer_id;
        if ($bag->save()) {
            return response()->json(['sucsess' => 1], 200);
        }

        // if ($has_bag) {
        //     return response()->json(['sucsess' => 0], 200);
        // } else {
        //     $bag = new Bag();
        //     $bag->qrcode = $new_qr_code;
        //     $bag->service_id = $service_id;
        //     if ($bag->save()) {
        //         $customer_bag =  new CustomerBag();
        //         $customer_bag->customer_id = $customer_id;
        //         $customer_bag->bag_id = $bag->id;
        //         if ($customer_bag->save()) {
        //             return response()->json(['sucsess' => 1], 200);
        //         }
        //     }
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CustomerBag  $customerBag
     * @return \Illuminate\Http\Response
     */
    public function show($customer_id)
    {
        $customerBag = Bag::with(['Service', 'OrderBags'])->where('customer_id', $customer_id)->get();
        return response()->json(['success' => 1, "data" => $customerBag], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CustomerBag  $customerBag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerBag $customerBag)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomerBag  $customerBag
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerBag $customerBag)
    {
    }

    public function delete($id)
    {
        $cb =   Bag::destroy($id);
        return ['success' => true];
    }

    public function unatach($id)
    {
        $bag = Bag::find($id);
        $bag->customer_id = null;
        $bag->save();
        return  ['success' => true, 'bag' => $bag];
    }
}
