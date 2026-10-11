<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerAddress;
use Illuminate\Http\Request;
use Validator;

class CustomerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $customers = Customer::orderBy('id', "desc")->get();
        return view('admin.customer.index', compact('customers'));
    }

    public function locations()
    {
        return view('admin.order.location');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.customer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "email" => 'required|email|unique:customers',
            "phone" => 'required|string|unique:customers',
        ]);

        $customer = new Customer();
        $customer->first_name = $request->customer_first_name;
        $customer->last_name = $request->customer_last_name;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        if ($customer->save()) {
            $customer_address = new CustomerAddress();
            $customer_address->customer_id = $customer->id;
            $customer_address->type_id = $request->address_type;
            $customer_address->location_id = $request->Location;
            $customer_address->building_no = $request->bldg_no;
            $customer_address->street_address = $request->street_name;
            $customer_address->floor_no = $request->floor_numner;
            $customer_address->apartment_no = $request->apt;
            $customer_address->additional_directions = $request->additional;
            $customer_address->latitude = $request->lat;
            $customer_address->longitude = $request->lng;
            if ($customer_address->save()) {
                return "<h3>The New Customer ID</h3> <h1>" . $customer->id . "</h1> <br> <h4>Please Copy ID</h4> <br><a href='/orders'>Bcak to orders</a>";
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // return "ok";
        $Customer = Customer::find($id);
        return [
            'customer' => $Customer,
            'address' =>  $Customer->CustomerAddresses,
        ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $Customer = Customer::find($id);
        $detail = [
            'customer' => $Customer,
            'address' =>  $Customer->CustomerAddresses,
        ];
        return view('admin.customer.edit', compact('detail'));
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
        if (
            $request->customer_first_name == '' ||
            $request->customer_last_name == '' ||
            $request->phone == '' ||
            $request->email == ''
        ) {
            return "Invalid Entries";
        }

        // return $request;
        $Customer = Customer::find($id);
        $Customer->first_name = $request->customer_first_name;
        $Customer->last_name = $request->customer_last_name;
        $Customer->phone = $request->phone;
        $Customer->email = $request->email;
        $Customer->update();
        return redirect('customers/' . $id . '/edit');
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
}
