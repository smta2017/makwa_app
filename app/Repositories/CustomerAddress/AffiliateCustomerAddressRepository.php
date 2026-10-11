<?php

namespace App\Repositories\CustomerAddress;

use App\CustomerAddress;
use App\Http\Resources\V1\CustomerAddress\AffiliateCustomerAddress;

class AffiliateCustomerAddressRepository implements ICustomerAddress
{

    public function store($request)
    {
        $CustomerAddress =  new CustomerAddress();
        $CustomerAddress->customer_id = $request->customer_id;
        $CustomerAddress->type_id = $request->type_id;
        $CustomerAddress->location_id = $request->location_id;
        $CustomerAddress->latitude = $request->latitude;
        $CustomerAddress->longitude = $request->longitude;
        $CustomerAddress->building_no = $request->building_no;
        $CustomerAddress->street_address = $request->street_address;
        $CustomerAddress->affiliate_id = auth()->user()->id;
        $CustomerAddress->save();
        return new AffiliateCustomerAddress($CustomerAddress);
    }
}
