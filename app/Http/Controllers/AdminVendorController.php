<?php

namespace App\Http\Controllers;

use App\Http\Resources\V1\Vendor\VendorResource;
use App\Repositories\Vendor\VendorRepository;
use App\Vendor;
use App\VendorProduct;
use App\VendorProductPrice;
use Illuminate\Http\Request;

class AdminVendorController extends Controller
{

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
        return view('admin.vendor.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vendor = Vendor::find($id);
        $hasProduct = VendorProduct::where('vendor_id', $id)->exists();
        if (!$hasProduct) {
            \DB::select("insert into vendor_products (vendor_id,product_id) select " . $id . ",id from products");
        }
        return view('admin.vendor.edit', compact('vendor'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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


    public function addvendorproduct(Request $request)
    {
        $vendorpp = new VendorProductPrice();
        $vendorpp->vendor_id = $request->vendor_id;
        $vendorpp->vendor_product_id = $request->item;
        $vendorpp->service_id = $request->service;
        $vendorpp->price = $request->price;
        $vendorpp->save();
        return redirect("/admin/vendor/" . $request->vendor_id);
    }


    public function archiveItem(Request $request)
    {
        // return "okk";
        $vendorpp = VendorProductPrice::find($request->vpp_id);
        $vendorpp->is_archive = ($vendorpp->is_archive) ? null : 1;
        $vendorpp->save();
        return redirect("/admin/vendor/" . $request->vendor_id);
    }
}
