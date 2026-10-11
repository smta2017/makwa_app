<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function fromAgent()
    {
        return view('admin.facility.fromAgent');
    }

    public function orderByCustomer(Request $request){
        $validatedData = $request->validate([
            'id' => 'required|numeric',
        ]);
       $orders = Order::where('customer_id',$request->id)->get();
       return view('admin.facility.formAgentService',compact('orders'));
    }
}
