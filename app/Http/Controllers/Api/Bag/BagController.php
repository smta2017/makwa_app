<?php

namespace App\Http\Controllers\Api\Bag;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Bag;
use App\Customer;
use App\Http\Resources\Customer\CustomerBagResource;
use App\Http\Resources\Order\OrderScanBagResource;
use App\Order;
use App\CustomerBag;
use App\Http\Resources\Bag\Bag as BagBag;
use App\Http\Resources\Order\OrderVendorResource;
use App\Lib\NotifyGetaway;
use App\Lib\ResponseFormat;
use App\Notifications\OrderDelivered;
use App\orderHangLable;
use App\OrderService;
use App\OrderBag;
use App\Repositories\Bag\BagRepository;
use App\Repositories\Order\OrderRepository;
use Auth;
use Illuminate\Http\JsonResponse;

class BagController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:admin-api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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


    // ===========================================================================================

    public function updateQrStatus()
    {
        // Bag::whereNull('print_count')->increment('print_count', 1);
        \DB::table('bags')->increment('print_count');
        return response()->json(['sucsess' => true], 200);
    }
    public function generateQRCode(Request $request)
    {
        $counter = (int) $request->counter;
        $service_id = (int) $request->service_id;
        $reusable = $request->reusable;

        $maxBagId = (int) bag::where('id', bag::max('id'))->first('qrcode')['qrcode'] + 1;
        if ($maxBagId == '') {
            $maxBagId = 1;
        }
        for ($i = $maxBagId; $i < $maxBagId + $counter; $i++) {

            $bag = new Bag();
            $bag->qrcode = sprintf('%1$05d', $i);
            $bag->service_id = $service_id;
            if ($reusable == "true") {
                $bag->customer_id = 0;
            }
            $bag->save();
        }
    }

    public function getFreeBags()
    {
        $freeBag = Bag::whereNotIn('id', OrderBag::get('bag_id'))->where('print_count', 0)->get();
        return BagBag::collection($freeBag);
    }

    public function printQr(Request $request, $qrcode)
    {
        $bag =  Bag::where('qrcode', $qrcode)->first();
        $res = [
            "qrc" => $bag["qrcode"],
            "customer_id" => $bag["customer_id"],
            "customer_name" => $bag["Customer"]["first_name"] . ' ' .  $bag["Customer"]["last_name"],

            // 'avatar' => '<div class="visible-print text-center">
            //                 {!! QrCode::size(100)->generate('. $bag["qrcode"] .'); !!}
            //                 <h2><strong>{{'. $bag["qrcode"] .'}}</strong></h2>
            //             </div>',
        ];


        return view("test", compact('res'));
    }

    public function scanQr(Request $request, $rqcode)
    {
        $bag = Bag::where("qrcode", $rqcode)->first();

        if ($bag != null) {

            if (Auth::guard("admin-api")->check()) {

                $order_service_id =   OrderBag::where('bag_id', $bag->id)->first("order_service_id");
                $order_service = OrderService::find($order_service_id["order_service_id"]);
                if ($order_service != null) {
                    $orderf = Order::where("id", $order_service->order_id)->where('status_id', "<>", 3)->first();
                    if ($orderf != null) {
                        return response()->json(["success" => 1, "opr" => "bagbuasy",], 200);
                    }
                }

                if ($bag->customerBags->count()) {
                    $customer_order = $bag->customerBags[0]->Customer->Orders->where("status_id", 1)->whereNull("canceled_at")->where('pickup_date', date('Y-m-d'))->first();
                    if ($customer_order == null) {
                        return response()->json(["success" => 0, "message" => "no orders for customer detacted"], 200);
                    }
                    $data = [
                        "order" => $customer_order,
                        "bag" => $bag
                    ];
                    return new OrderScanBagResource($data);
                } else {

                    $Order = Order::select("customer_id")->groupBy("customer_id")->where("status_id", 1)->whereNull("canceled_at")->where('pickup_date', date('Y-m-d'))->havingRaw('count(customer_id) = ?', [1])->get();
                    $customer = Customer::whereIn("id", $Order)->get();

                    $data = [
                        "success" => 1,
                        "opr" => "new",
                        "bag" => $bag,
                        "customers" => CustomerBagResource::collection($customer)
                    ];
                    return response()->json($data);
                }
            } elseif (Auth::guard("vendor-api")->check()) {
                // ========================= Vendor
                $OrderServiceids = OrderBag::where("bag_id", $bag->id)->get("order_service_id");
                $orderids = OrderService::whereIn("id", $OrderServiceids)->get("order_id");
                $order = Order::whereIn("id", $orderids)->where("status_id", 7)->where("vendor_id", Auth::guard("vendor-api")->user()->id)->where('pickup_date', date('Y-m-d'))->first();
                // log
                if ($order == null) {
                    return response()->json(["success" => 0, "message" => "no orders for customer bag"], 200);
                }

                $data = [
                    "success" => 1,
                    "opr" => "fromagent",
                    "bag" => $bag,
                    "order" => new OrderVendorResource($order),
                ];

                return response()->json($data);
            }
        } else {
            return response()->json(["success" => 0, "message" => "no bag detacted"], 200);
        }
    }

    public function linkbag(Request $request)
    {
        $customerBag =  new CustomerBag();
        $customerBag->customer_id = $request->customer_id;
        $customerBag->bag_id = $request->bag_id;
        if ($customerBag->save()) {
            return response()->json(["success" => 1], 200);
        } else {
            return response()->json(["success" => 0], 200);
        };
    }



    public function fromVendor(Request $request)
    {
        $order = Order::find($request->order_id);
        $bag =  Bag::where('qrcode', $request->qrcode)->first();

        $order_service_id = $order->OrderServices->where("service_id", $bag->service_id)->first();
        $OrderBag = OrderBag::where("order_service_id", $order_service_id["id"])->where("bag_id", $bag->id)->first();
        if ($OrderBag != null) {
            $OrderBag->from_vendor = date('Y-m-d H:i:s');
            if ($OrderBag->update()) {
                return response()->json(['success' => 1, 'message' => "agent_recipt_from_vendor"], 200);
            } else {
                return response()->json(['success' => 0, 'message' => "confirm error!"], 200);
            }
        } else {
            return response()->json(['success' => 0, 'message' => "no order service for this bag"], 200);
        }
    }

    public function toCustomer(Request $request)
    {
        $order = Order::find($request->order_id);
        $bag =  Bag::where('qrcode', $request->qrcode)->first();

        $order_service_id = $order->OrderServices->where("service_id", $bag->service_id)->first();
        $OrderBag = OrderBag::where("order_service_id", $order_service_id["id"])->where("bag_id", $bag->id)->first();
        if ($OrderBag != null) {
            $OrderBag->to_customer = date('Y-m-d H:i:s');
            if ($OrderBag->update()) {
                return response()->json(['success' => 1, 'message' => "agent_confirm_to_customer"], 200);
            } else {
                return response()->json(['success' => 0, 'message' => "confirm error!"], 200);
            }
        } else {
            return response()->json(['success' => 0, 'message' => "no order service for this bag"], 200);
        }
    }

    public function fromVendorHang(Request $request)
    {
        $orderHang = orderHangLable::where('lable_code', $request->lable_code)->first();
        if ($orderHang == null) {

            return response()->json(['success' => 0, 'message' => "No lable detected!"], 200);
        }
        if ($orderHang->status != null) {
            return response()->json(['success' => 0, 'message' => "Lable Busy!"], 200);
        }

        if ($orderHang->order_id != $request->order_id) {
            return response()->json(['success' => 0, 'message' => "Order not machted!"], 200);
        }

        $orderHang->from_vendor = date('Y-m-d H:i:s');
        if ($orderHang->update()) {
            return response()->json(['success' => 1, 'message' => "agent_recipt_hanged_item_from_vendor"], 200);
        } else {
            return response()->json(['success' => 0, 'message' => "confirm error!"], 200);
        }
    }

    public function toCustomerHang(Request $request)
    {
        $orderHang = orderHangLable::where('lable_code', $request->lable_code)->first();
        if ($orderHang == null) {

            return response()->json(['success' => 0, 'message' => "No lable detected!"], 200);
        }
        if ($orderHang->status != null) {
            return response()->json(['success' => 0, 'message' => "Lable Busy!"], 200);
        }

        if ($orderHang->order_id != $request->order_id) {
            return response()->json(['success' => 0, 'message' => "Order not machted!"], 200);
        }

        $orderHang->to_customer = date('Y-m-d H:i:s');
        if ($orderHang->update()) {
            return response()->json(['success' => 1, 'message' => "agent_recipt_hanged_item_from_vendor"], 200);
        } else {
            return response()->json(['success' => 0, 'message' => "confirm error!"], 200);
        }
    }

    public function confirmPrepareRoute(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->confirm_prepare_route = 1;
        if ($order->update()) {
            return response()->json(['success' => 1, 'message' => "route confirm"], 200);
        } else {
            return response()->json(['success' => 0, 'message' => "confirm error!"], 200);
        }
    }


    public function scanBag(OrderRepository $orderRepository, BagRepository $bagRepository, Request $request)
    {
        try {
            $bag = bag::where('qrcode', $request->qrcode)->firstOrFail();
            $request["bag_id"] = $bag["id"];
            $order = Order::find($request->order_id);
            if ($bag["customer_id"] == "0") {
                $bag->customer_id = $order->customer_id;
                $bag->save();
                return ResponseFormat::sucsess(['status' => 'fixedbag'], 'Succsess.', JsonResponse::HTTP_OK);
            } else if (is_null($bag["customer_id"])) {
                $bagExists = OrderBag::where('bag_id', $request["bag_id"])->exists();
                if ($bagExists) {
                    return ResponseFormat::error('', 'the bag already used.', JsonResponse::HTTP_OK);
                }
            } else {
                if ($order->customer_id != $bag["customer_id"]) {
                    return ResponseFormat::error('', 'not customer bag.', JsonResponse::HTTP_OK);
                } else {
                    $isBusy = OrderBag::where('bag_id', $request["bag_id"])->whereHas("Order", function ($q) {
                        $q->where("status_id", "<>", 3);
                        $q->noneCanceled();
                    })->exists();
                    if ($isBusy) {
                        return ResponseFormat::error('', 'the bag already used.', JsonResponse::HTTP_OK);
                    }
                }
            }

            // if (is_null($bag["customer_id"]) || $bag["customer_id"]!=0) {
            $receptBag = $bagRepository->receptBagfromCustomer($request);
            // }
            $orderBags = $orderRepository->getScanedOrderBags($request->order_id);

            if ($receptBag['success'] == 1) {
                return ResponseFormat::sucsess(['bags' => $orderBags], 'Succsess.', JsonResponse::HTTP_OK);
            } else {
                return ResponseFormat::error('bag not recept', 'Something Went Wrong.', JsonResponse::HTTP_OK);
            }
        } catch (\Throwable $th) {
            return ResponseFormat::error($th->getMessage(), 'Something Went Wrong.', JsonResponse::HTTP_OK);
        }
    }

    public function getScanedOrderBags($id, OrderRepository $orderRepository)
    {
        try {
            $orderBags = $orderRepository->getScanedOrderBags($id);
            $reusebleBags = $orderRepository->getReusableBags($id);
            if ($orderBags == 'recepted') {
                return ResponseFormat::error('', 'Order Recepted.', JsonResponse::HTTP_OK);
            }
            return ResponseFormat::sucsess(['bags' => $orderBags, 'reuseble_bags' => $reusebleBags], 'Succsess.', JsonResponse::HTTP_OK);

            return $orderBags;
        } catch (\Throwable $th) {
            return ResponseFormat::error($th->getMessage(), 'Something Went Wrong.', JsonResponse::HTTP_OK);
        }
    }

    public function deleteBag(BagRepository $bagRepository, Request $request)
    {
        $bag = $bagRepository::removeOrderBagById($request->id);
        return ResponseFormat::sucsess(['bags' => $bag], 'Succsess.', JsonResponse::HTTP_OK);
    }

    public function customerPay($customer_id, Request $request)
    {
        $customer = Customer::find($customer_id);
        $customer->depositFloat($request->amount, ['description' => 'Dep']);

        $usr = Auth::user()->name;
        $order =  Order::find($request->order_id);
        $order->delivery_user = $usr;
        $order->status_id = 3;
        $order->wallet_val = $customer->balanceFloat;
        $order->deliver_at = date('Y-m-d H:i:s');
        $order->save();

        // Notify Customer
        $title = "Order Delivered";
        $body = "Hi " . $order->Customer->first_name . ", this to confirm that your order has been delivered at " . date('h:i') . ". Looking forward to your next one.";
        $noti = new OrderDelivered($order->Customer->first_name, $title, $body);
        NotifyGetaway::sendNotification($order->Customer, $noti, $body, "orderDetails", $request->order_id, $title);
        return $customer->balanceFloat;
    }
}
