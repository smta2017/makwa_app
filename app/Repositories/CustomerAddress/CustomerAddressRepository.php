<?php

namespace App\Repositories\CustomerAddress;

use App\CustomerAddress;
use App\Http\Resources\V1\CustomerAddress\CustomerAddress as CustomerAddressResources;

class CustomerAddressRepository implements ICustomerAddress
{
    public function currentCustomerAddesses()
    {
        $customerAddress = CustomerAddress::whereNull('deleted_at')->where('customer_id', auth()->user()->id)->get();
        return CustomerAddressResources::collection($customerAddress);
    }
    public function store($request)
    {
        $customerAddress =  new CustomerAddress();
        $customerAddress->customer_id = auth()->user()->id;
        $customerAddress->type = $request->type;
        $customerAddress->location_id = $request->location_id;
        $customerAddress->latitude = $request->latitude;
        $customerAddress->longitude = $request->longitude;
        $customerAddress->building_no = $request->building_no;
        $customerAddress->street_address = $request->street_address;
        $customerAddress->floor_no = $request->floor_no;
        $customerAddress->apartment_no = $request->apartment_no;
        $customerAddress->additional_directions = $request->additional_directions;
        $customerAddress->country_code = $request->country_code;
        $customerAddress->phone_number = $request->phone_number;
        $customerAddress->landline = $request->landline;
        $customerAddress->building_type = $request->building_type;
        $customerAddress->save();
        return  new CustomerAddressResources($customerAddress);
    }

    public function destroy($id)
    {
        $customerAddress = CustomerAddress::find($id)->update(['deleted_at'=> date('Y-m-d H:i:s')]);
        return  $customerAddress;
    }
    public function setDefault($id)
    {
        CustomerAddress::where('customer_id', auth()->user()->id)->update(['is_default' => 0]);
        $customerAddress = CustomerAddress::find($id);
        $customerAddress->is_default = 1;
        $customerAddress->save();
        return  new CustomerAddressResources($customerAddress);
    }
}
