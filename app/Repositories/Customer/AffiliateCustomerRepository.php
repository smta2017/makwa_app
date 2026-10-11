<?php

namespace App\Repositories\Customer;

use App\Customer;
use Illuminate\Support\Facades\Hash;

class AffiliateCustomerRepository implements ICustomer
{
  public function store($request)
  {
    $customer = new Customer();
    $customer->first_name = $request['first_name'];
    $customer->last_name = $request['last_name'];
    $customer->email = $request['email'];
    $customer->phone = $request['phone'];
    $customer->affiliate_id = auth()->user()->id;
    $customer->save();
    return  $customer;
  }


}
