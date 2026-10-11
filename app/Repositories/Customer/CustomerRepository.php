<?php

namespace App\Repositories\Customer;

use App\Customer;
use App\FavouritVendor;
use App\Http\Resources\V1\Customer\Customer as CustomerCustomer;
use App\Http\Resources\V1\Customer\CustomerOrders;
use App\Order;
use Illuminate\Support\Facades\Hash;

class CustomerRepository implements ICustomer
{
  public function store($request)
  {
    $customer = new Customer();
    $customer->first_name = $request['first_name'];
    $customer->last_name = $request['last_name'];
    $customer->email = $request['email'];
    $customer->phone = $request['phone'];
    $customer->country_code = $request['country_code'];
    $customer->password = Hash::make($request['password']);
    $customer->firebase_id = Hash::make($request['firebase_token']);
    $customer->save();
    return  $customer;
  }

  public static function GetCustomerByPhone($phone)
  {
    return Customer::where('phone', $phone)->first();
  }

  public function getCustomerOrders()
  {
    $customerOrders = Order::with('Reviews')->noneCanceled()->where('customer_id', auth()->user()->id)->orderBy('id','Desc')->get();
    // return $customerOrders;
    return CustomerOrders::collection($customerOrders);
  }

  public function getCustomerDetails()
  {
    return new CustomerCustomer(auth()->user());
  }

  public static function changeEmail($newEmail)
  {
    return Customer::find(auth()->user()->id)->update(['email' => $newEmail]);
  }

  public static function changePass($newPass)
  {
    return Customer::find(auth()->user()->id)->update(['password' =>  \Hash::make($newPass)]);
  }

  public static function changePhone($newPhone, $country_code)
  {

    return Customer::find(auth()->user()->id)->update(['phone' => $newPhone, 'country_code', $country_code]);
  }

  public static function changeInfo($first_name, $last_name, $d_o_b, $gender)
  {
    return Customer::find(auth()->user()->id)->update(['first_name' => $first_name, 'last_name' => $last_name, 'DOB' => $d_o_b, 'gender' => $gender]);
  }

  public static function CustomerFavouritVendors()
  {
    $customer = auth()->user();
    $vendors = [];
    foreach ($customer->FavouritVendors as  $value) {
      array_push($vendors, $value->vendor);
    }

    return $vendors;
  }

  public static function addFavouritVendor($id)
  {
    return FavouritVendor::create(["vendor_id" => $id, 'customer_id' => auth()->user()->id]);
  }

  public static function removeFavouritVendor($id)
  {
    return FavouritVendor::where("vendor_id", $id)->where('customer_id', auth()->user()->id)->delete();
  }
}
