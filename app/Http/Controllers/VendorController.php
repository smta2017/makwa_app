<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderService;
use App\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:vendor');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function ordersPage()
    {
        return view('vendor.order.index');
    }

    public function index()
    {
        return view('vendor.index');
    }

    public function pickupsort()
    {
        // return "PS";
        return view('vendor.order.pickupsort');
    }


    public function deliverysort()
    {
        return view('vendor.order.deliverysort');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function scanqr()
    {
        // return "ccc";
        return view('vendor.scanqr.scanqrcode');
    }



    public function showOrderproduct(Request $request)
    {
        // $OrderService = OrderService::find($request->osid);
        $order = Order::find($request->oid);
        $sevicesid = OrderService::where('order_id', $request->oid)->where('vendor_id', \Auth::guard('vendor')->user()->id)->get();

        // return $sevicesid;
        return view('vendor.show', compact('order', 'sevicesid'));
    }



    public function orderinvoice()
    {
        return view('vendor.sortinvoice');
    }

    public function fromAgent()
    {
        return view('vendor.order.fromAgent');
    }

    public function deliveryInvoice(Request $request)
    {
        return view('vendor.deinvoice');

    }
}
