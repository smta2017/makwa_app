<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Vendor\VendorResource;
use App\Http\Resources\V1\Vendor\VendorResourceDashboard;
use App\Http\Resources\VendorSlotsResource;
use App\Lib\ResponseFormat;
use App\Repositories\Vendor\VendorRepository;
use App\Vendor;
use App\VendorSlotsTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function getVendors(VendorRepository $vendorRepository)
    {
        try {
            $vendors = VendorResourceDashboard::collection($vendorRepository->getAllVendors());
            return ResponseFormat::sucsess($vendors, 'Succsess Returned Vendors.', JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            return ResponseFormat::error($th->getMessage(), 'Something Went Wrong.', JsonResponse::HTTP_OK);
        }
    }

    public function getVendorDetails($id, VendorRepository $vendorRepository)
    {
        try {
            $vendor = new VendorResource($vendorRepository->getVendorDetails($id));
            return ResponseFormat::sucsess($vendor, 'Succsess Returned Vendors.', JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            return ResponseFormat::error($th->getMessage(), 'Something Went Wrong.', JsonResponse::HTTP_OK);
        }
    }

    public function store(Request $request)
    {
        $vendor = new Vendor();
        $vendor->name = $request->vname;
        $vendor->email = $request->email;
        $vendor->max_orders = $request->max_orders;
        $vendor->min_order_price = $request->min_order_price;
        $vendor->delivery_fee = $request->delivery_fee;
        $vendor->password = \Hash::make('123456');

        $vendor->save();
        return $vendor;
    }

    public function vendorSlots(Request $request)
    {
        $vendorSlot =  VendorSlotsTime::where('vendor_id', $request->vendor_id)->get();
        return VendorSlotsResource::collection($vendorSlot);
    }
    public function delvendorSlots($id)
    {
        $vendorSlot =  VendorSlotsTime::destroy($id);
        return 1;
    }

    
}
