<?php

namespace App\Http\Controllers\Api\Helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\GlobalSetting;
use App\Customer;
use App\Http\Resources\CustomerProfile as CustomerProfileResource;
use App\Http\Resources\CustomerOrder as CustomerOrderResource;

class UnanimousHelperController extends Controller
{

    public function updateSignedOut(Request $request)
    {
        if ($request->getMethod() == 'OPTIONS') {
            return json_encode(array('status' => 0), JSON_NUMERIC_CHECK);
        }
        $customer = Customer::find($request["CustomerId"]);
        $customer->is_signedOut = 1;
        if ($customer->update()) {
            $success_arr = array(
                "status" => 1,
                "message" => "Signed Out Updated Successfully."
            );
        } else {
            $success_arr = array(
                "status" => 0,
                "message" => "Signed Out Updation Failed."
            );
        }
        return json_encode($success_arr, JSON_NUMERIC_CHECK);
    }

    public function getCustomerProfile($id = null, Request $request)
    {
        if ($request->getMethod() == 'OPTIONS') {
            return json_encode(array('status' => 0), JSON_NUMERIC_CHECK);
        }
        $id = $request["CustomerId"];
        $customer = Customer::find($id);
        $version = GlobalSetting::select('value')->where('setting_name', 'version_no')->first();
        $customer->version_no = $version->value;
        return new CustomerProfileResource($customer);
    }

    public function getCustomerOrders(Request $request)
    {
        if ($request->getMethod() == 'OPTIONS') {
            return json_encode(array('status' => 0), JSON_NUMERIC_CHECK);
        }
        $id = $request["CustomerId"];
        $Customer = Customer::find($id);
        $globalSetting = GlobalSetting::get();
        $Customer->cancellation_fee         = $globalSetting->where('setting_name', 'cancellation_buffer')->first()->value;
        $Customer->cancellation_buffer      = $globalSetting->where('setting_name', 'cancellation_fee')->first()->value;
        $Customer->pickupRescStartBuffer    = $globalSetting->where('setting_name', 'rescheduling_buffer_start')->first()->value;
        $Customer->deliveryRescEndBuffer    = $globalSetting->where('setting_name', 'rescheduling_buffer_end')->first()->value;
        $Customer->rescheduling_fee         = $globalSetting->where('setting_name', 'rescheduling_fee')->first()->value;
        return new CustomerOrderResource($Customer);
    }


    public function getPersonalCode(Request $request)
    {
        // $this->authenticate($request);
        // return json_encode(array('status' => 0), JSON_NUMERIC_CHECK);
        $success_arr = array(
            'status' => 1,
            'message' => 'Successfully promotions orders against customer.',
            'code' => [
                'id' => 4759,
                'name' => "e3augQ",
                'customer_id' => 4906,
                'created_at' => "2020-01-15 20:48:57",
                'updated_at' => "2020-01-15 20:48:57",
                'discount_max' => 25
            ]
        );
        return json_encode($success_arr, JSON_NUMERIC_CHECK);
    }

  }
