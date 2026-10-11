<?php

namespace App\Http\Controllers;

use App\Vendor;
use App\VendorPromotion;
use Illuminate\Http\Request;

class VendorPromotionController extends Controller
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
    public function index(Request $request)
    {
        $vendor = Vendor::find($request->vendor_id);
        $vendor_promotion = VendorPromotion::with(['Vendor'])->where('vendor_id', $request->vendor_id)->get();
        return view('admin.vendor_Promotion.index', compact('vendor_promotion','vendor'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $vendor_id = $request->vendor_id;
        return view('admin.vendor_Promotion.add', compact('vendor_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $vendorPromotion =  new VendorPromotion();
        $vendorPromotion->type = $request->type;
        $vendorPromotion->discount_figure = $request->discount_figure;
        $vendorPromotion->cap = $request->cap;
        $vendorPromotion->usage_limit = $request->usage_limit;
        $vendorPromotion->min_order = $request->min_order;
        $vendorPromotion->start_date = $request->start_date;
        $vendorPromotion->end_date = $request->end_date;
        $vendorPromotion->vendor_id = $request->vendor_id;
        $vendorPromotion->applay_on_delivery = ($request->delivery_fee == "on") ? 1 : 0;
        $vendorPromotion->save();
        return redirect("/admin/vendor-offres?vendor_id=" .  $vendorPromotion->vendor_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VendorPromotion  $vendorPromotion
     * @return \Illuminate\Http\Response
     */
    public function show(VendorPromotion $vendorPromotion)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\VendorPromotion  $vendorPromotion
     * @return \Illuminate\Http\Response
     */
    public function edit($id, VendorPromotion $vendorPromotion)
    {
        $vendorPromotiond = VendorPromotion::find($id);
        return view('admin.vendor_Promotion.edit', compact('vendorPromotiond'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VendorPromotion  $vendorPromotion
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $vendorPromotion =  VendorPromotion::find($id);
        $vendorPromotion->type = $request->type;
        $vendorPromotion->discount_figure = $request->discount_figure;
        $vendorPromotion->cap = $request->cap;
        $vendorPromotion->usage_limit = $request->usage_limit;
        $vendorPromotion->min_order = $request->min_order;
        $vendorPromotion->start_date = $request->start_date;
        $vendorPromotion->end_date = $request->end_date;
        $vendorPromotion->applay_on_delivery = ($request->delivery_fee == "on") ? 1 : 0;
        $vendorPromotion->save();
        return redirect("/admin/vendor-offres?vendor_id=" .  $vendorPromotion->vendor_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VendorPromotion  $vendorPromotion
     * @return \Illuminate\Http\Response
     */
    public function destroy(VendorPromotion $vendorPromotion)
    {
        //
    }
}
