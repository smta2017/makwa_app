<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Bag;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\bag\bag as BagResource;
use App\Http\Resources\Vendor\VendorOrdersResource;
use Illuminate\Http\Request;
use App\Vendor;
use App\Http\Resources\Vendor\VendorResource;
use App\Http\Resources\V1\Vendor\VendorResourceDashboard;
use App\Http\Resources\Vendor\VendorServiceResource;
use App\Lib\ResponseFormat;
use App\Order;
use App\OrderService;
use App\OrderBag;
use App\Repositories\Bag\BagRepository;
use App\Repositories\Vendor\VendorRepository;
use Illuminate\Http\JsonResponse;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:vendor-api']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return phpinfo();
        return \QRCode::text('QR Code Generator for Laravel!')->png();
        return VendorResource::collection(Vendor::get());
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
        return new VendorResource(Vendor::find($id));
    }

    public function getVendorDetails(VendorRepository $vendorRepository)
    {
        $vid = \Auth::guard('vendor-api')->user()->id;

        try {
            $vendor = new VendorResourceDashboard($vendorRepository->getVendorDetails($vid));
            return ResponseFormat::sucsess($vendor, 'Succsess Returned Vendors.', JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            return ResponseFormat::error($th->getMessage(), 'Something Went Wrong.', JsonResponse::HTTP_OK);
        }
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

    // ================================= CUSTOM API FUNCTIONS ==========================================
    public function vendor_service($id)
    {
        $vendor_services = OrderService::where("vendor_id", $id)->get();
        return VendorServiceResource::collection($vendor_services);
    }

    public function vendor_orders(Request $request)
    {
        $vid = \Auth::guard('vendor-api')->user()->id;
        $orders = Order::where("vendor_id", $vid)
            ->whereNull("canceled_at")
            // ->where("status_id", $request["status_id"])
            ->orderBy('id', 'Desc');
        if ($request->order_id != "") {
            $orders->where("id", $request->order_id);
        }
        if ($request->customer_id != "") {
            $orders->where("customer_id", $request->customer_id);
        }

        return VendorOrdersResource::collection($orders->paginate(100));
    }


    public function getAgentOrders($id, BagRepository $bagRepository)
    {
        try {
            $orderBags = $bagRepository->getAgentBags($id);
            return ResponseFormat::sucsess(['bags' => BagResource::collection($orderBags)], 'Succsess.', JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            return ResponseFormat::error($th->getMessage(), 'Something Went Wrong.', JsonResponse::HTTP_OK);
        }
    }

    public function scanBag(BagRepository $bagRepository, Request $request)
    {
        try {
            $request["bag_id"] = Bag::where('qrcode', $request->qrcode)->firstOrFail('id')['id'];
            $OrderBag = OrderBag::where('bag_id', $request["bag_id"])->whereNull('from_agent')->first();
            $request["order_id"] = $OrderBag->order_id;

            $receptBag = $bagRepository->receptBagfromAgent($request);

            return ResponseFormat::sucsess('', 'Succsess.', JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            return ResponseFormat::error($th->getMessage(), 'Something Went Wrong.', JsonResponse::HTTP_OK);
        }
    }


    public function scanBagToSort(BagRepository $bagRepository, Request $request)
    {
        try {
            $bag1 = Bag::where('qrcode', $request->qrcode)->firstOrFail('id');
            $request["bag_id"] =  $bag1['id'];
            $bag = Bag::findOrFail($request["bag_id"]);

            if ($request['opr'] == 'delivery') {
                $ob = OrderBag::where('bag_id', $request["bag_id"])->whereNull('delivery_sorted')->first();
            } elseif ($request['opr'] == 'pickup') {
                $ob = OrderBag::where('bag_id', $request["bag_id"])->whereNull('pickup_sorted')->first();
            }
            
            $orderStatusId = $ob->Order->status_id;
            if (($request->opr == 'pickup' && $orderStatusId != 2) || ($request->opr == 'delivery' && $orderStatusId != 6)) {
                return ResponseFormat::error('not_in_sort', '', JsonResponse::HTTP_OK);
            }
            $customer_order_count = 0;

            $order_bags =  $bagRepository::getOrderBagsByOrderId($ob->Order->id);
            $orders_count =  $ob->Order->Customer->Orders->count();
            if (!is_null($bag->customer_id) && $orders_count = 54) {
                $customer_order_count = 1;
            }


            return ResponseFormat::sucsess(['order_bag' => $ob, 'bag' => $bag, 'order' => $ob->Order, 'order_bags' => $order_bags, 'customer_order_count' => 1, 'customer' => $ob->Order->Customer], 'Succsess.', JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            return ResponseFormat::error($th->getMessage(), 'Something Went Wrong.', JsonResponse::HTTP_OK);
        }
    }
    // ================================= HELPER API FUNCTIONS ==========================================


}
