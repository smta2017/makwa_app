<?php

namespace App\Http\Controllers\Api\Order;

use App\Customer;
use App\CustomerAddress;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\GlobalSetting;
use App\Http\Controllers\Api\Helper\OrderHelperController;
use App\Http\Controllers\facebook_events_api\facebookGateway;
use App\Http\Controllers\routific\routificController;
use App\Http\Helper\utilitiesHelper;
use App\Http\Resources\Order\AgentOrdersResource;
use App\Http\Resources\CustomerAddress as ResourcesCustomerAddress;
use App\Http\Resources\Order\AgentOrderDetails;
use App\Http\Resources\Order\reportOrdersResource;
use App\Order;
use App\OrderProduct;
use App\Http\Resources\OrderFastPreference as ResourcesOrderFastPreference;
use App\Http\Resources\OrderProduct as ResourcesOrderProduct;
use App\Http\Resources\ProductDetail;
use App\Jobs\UpdateRoutific;
use App\Jobs\withdrawalJob;
use App\Lib\NotifyGetaway;
use App\Lib\ResponseFormat;
use App\Notifications\AfterPickup;
use App\OrderFastPreference;
use App\OrderService;
use App\Promotion;
use App\Repositories\Order\OrderRepository;
use App\Repositories\OrderProduct\OrderProductRepository;
use App\Service;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use DB;
use Illuminate\Validation\ValidationData;
use Validator;

class OrderController extends Controller
{

    public function indextest()
    {
        return json_encode(array('status' => 0), JSON_NUMERIC_CHECK);
    }


    public static function checkmydate($date)
    {
        $tempDate = explode('-', $date);
        // checkdate(month, day, year)
        return checkdate($tempDate[1], $tempDate[2], $tempDate[0]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // =======================validation===============================
        $validatedData = Validator::make($request->json()->all(), [
            'customer_id' => 'required|numeric',
            'address_id' => 'required|numeric',
            'method_of_payment' => 'required|string|max:50',
            'pickup_date' => 'required',
            'pickup_start' => 'required|date_format:H:i:s',
            'pickup_end' => 'required|date_format:H:i:s',
            'delivery_date' => 'required',
            'delivery_start' => 'required|date_format:H:i:s',
            'delivery_end' => 'required|date_format:H:i:s',
            'promo_code' => 'max:20',
            'customised_preference' => 'required|numeric|between:0,9',
        ]);
        // =======================validation===============================

        if (!$validatedData->passes()) {
            return response()->json($validatedData->errors()->all(), 402);
        }

        $serviceFee = GlobalSetting::select('value')->where('setting_name', 'service_fee')->first();
        $tax = GlobalSetting::select('value')->where('setting_name', 'tax')->first();
        $order_request = json_decode($request->getContent(), true);

        $order = new Order();
        $order->customer_id = $request['CustomerId'];
        $order->address_id = $order_request['address_id'];
        $order->service_fee = $serviceFee->value;
        $order->tax_amount = $tax->value;
        $order->method_of_payment = $order_request['method_of_payment'];
        if ($order_request['method_of_payment'] == "card") {
            $order->selected_card = $order_request['selected_card'];
        }
        $order->pickup_date = $order_request['pickup_date'];
        $order->pickup_start = $order_request['pickup_start'];
        $order->pickup_end = $order_request['pickup_end'];
        $order->delivery_date = $order_request['delivery_date'];
        $order->delivery_start = $order_request['delivery_start'];
        $order->delivery_end = $order_request['delivery_end'];

        if (isset($order_request['promo_code']) && !empty($order_request['promo_code'])) {
            $promotion = Promotion::selectRaw('id,type,discount_figure,cap,start_date,end_date,usage_limit,min_order')
                ->where('name', $order_request['promo_code'])
                ->where('start_date', '<=', $order_request['pickup_date'])
                ->where('end_date', '>=', $order_request['pickup_date'])
                ->where('is_archive', 0)
                ->first();
            if (!$promotion == null) {
                $promoUsed = Order::selectRaw('count(id) as count')
                    ->where('customer_id', $request['CustomerId'])
                    ->where('promotion_id', $promotion->id)
                    ->first();
                if ($promotion->usage_limit == 0) {
                    $order->promotion_id = $promotion->id;
                } else if ($promotion->usage_limit - $promoUsed->count >= 0) {
                    $order->promotion_id = $promotion->id;
                }
            }
        }
        $order->status_id = 1;

        if ($order->delivery_date == "2020-NaN-NaN") {
            $date1 = $order->pickup_date;
            $date = new DateTime($date1);
            $date->add(new DateInterval('P1D'));
            $order->delivery_date = $date->format('Y-m-d');
            $order->nextday_fee = 0;
        }

        $order->vendor_id = OrderHelperController::orderRoutingVendor($order);


        if ($order->save()) {
            if ($order_request['customised_preference'] == 1) {
                foreach ($order_request['preferences'] as $preferences) {
                    $orderPreference = new OrderFastPreference();
                    $orderPreference->order_id = $order->id;
                    $orderPreference->product_id = $preferences['product_id'];
                    $orderPreference->preference = $preferences['preference'];
                    $orderPreference->save();
                }
            }

            $success_arr = array(
                'status' => 1,
                'message' => 'Successfully placed this order.',
                'order_id' => $order->id,
                'pickup_date' => $order_request['pickup_date'],
                'pickup_start_time' => $order_request['pickup_start'],
                'pickup_end_time' => $order_request['pickup_end'],
                'delivery_date' => $order_request['delivery_date'],
                'delivery_start_time' => $order_request['delivery_start'],
                'delivery_end_time' => $order_request['delivery_end'],
            );

            $custom_data = [
                "customer_id" => $request['CustomerId'],
                "value" => 0.00,
                "currency" => 'EGP'
            ];
            // facebookGateway::send_event("Purchase", $custom_data);


            // routificController::update_project($order->id);
            // UpdateRoutific::dispatch($order->id)->delay(now()->addMinutes(env('UPDATE_ROUTIFIC_TIME')));

            return response()->json($success_arr, 200);
        } else {
            $failure_arr = array(
                'status' => 0,
                'message' => "It's not you, its us... Something went wrong, please try again later.",
            );
            return response()->json($failure_arr, 200);
        };
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $this->changeOrderStatus();

        if (!$request->has('order_id')) {
            $failure_arr = array(
                'status' => 0,
                'message' => 'Parameter Missing - order_id should not be empty.',
            );
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
        } else {
            $request->validate([
                'order_id' => 'required|numeric',
            ]);
            $orderDetails = [];
            $orderId = $request->input('order_id');
            $order = Order::find($orderId);
            if (!empty($order)) {
                $subTotal = $order->OrderProducts->sum('ProductPrice.price');
                if ($order->OrderProducts->count()) {
                    if ($order->OrderProducts[0]->order_bag_id) {
                        $orderServices = OrderRepository::getOrderServices($order->id);
                        foreach ($orderServices as $key => $orderService) {
                            $products =  OrderProductRepository::getOrderProductsByService($order->id, $orderService->id);
                            array_push(
                                $orderDetails,
                                [
                                    "service" => $orderService->name,
                                    "products" => ResourcesOrderProduct::collection($products->groupBy('product_price_id'))
                                ]
                            );
                        }
                    } else {
                        $orderServices = $order->OrderServices;
                        foreach ($orderServices as $key => $orderService) {
                            $products =  OrderProduct::where('order_service_id', $orderService->id)->get();
                            array_push(
                                $orderDetails,
                                [
                                    "service" => $orderService->Service->name,
                                    "products" => ResourcesOrderProduct::collection($products->groupBy('product_price_id'))
                                ]
                            );
                        }
                    };
                }

                $subTotal = utilitiesHelper::orderSubTotal($orderId);

                $orderPreference = ResourcesOrderFastPreference::collection($order->OrderFastPreferencies);

                $promoDiscount = utilitiesHelper::promotionCalculate($order->id, $subTotal);
                $vendorpromoDiscount = utilitiesHelper::vendorPromotionCalculate($order->id, $subTotal);
                $total_amount = utilitiesHelper::totalAmountCalculate($order->id, $subTotal, $promoDiscount + $vendorpromoDiscount);

                $cancellationBuffer = GlobalSetting::select('value')->where('setting_name', 'cancellation_buffer')->first();
                $cancellation = GlobalSetting::select('value')->where('setting_name', 'cancellation_fee')->first();
                $pickupStartBuffer = GlobalSetting::select('value')->where('setting_name', 'rescheduling_buffer_start')->first();
                $pickupEndBuffer = GlobalSetting::select('value')->where('setting_name', 'rescheduling_buffer_end')->first();
                $rescheduling_fee = GlobalSetting::select('value')->where('setting_name', 'rescheduling_fee')->first();
                $wash_bag_count = OrderService::where("service_id", 4)->where("order_id", $order->id)->first('bag_count');
                $wash_bag_price = GlobalSetting::select('value')->where('setting_name', 'wash_bag_price')->first();
                $cancellation = $cancellation->value;

                if ($wash_bag_count != null) {
                    $wah_amount =  " + " . $wash_bag_price->value * $wash_bag_count->bag_count;
                } else {
                    $wah_amount =  "";
                }
                $order = array(
                    'order_id' => $order->id,
                    'status' => ($order->OrderStatus->name == 'Cleaning' || $order->OrderStatus->name == 'Pending Delivery' || $order->OrderStatus->name == 'Pending Vendor') ? "Order pickup" : $order->OrderStatus->name,
                    'address_id' => $order->address_id,
                    'total_price' => $total_amount,
                    'delivery_fee' => $order->delivery_fee,
                    'tax_amount' => $order->tax_amount,
                    'discount_amount' => $order->discount_amount,
                    'promotion_discount' => $order->promotion_discount,
                    'order_cancel' => $order->cancellation_fee,
                    'order_reschedule' => $order->reschedule_fee,
                    'sub_total' => $subTotal . $wah_amount,
                    'service_fee' => $order->service_fee,
                    'method_of_payment' => $order->method_of_payment,
                    'pickup_date' => $order->pickup_date,
                    'pickup_start' => $order->pickup_start,
                    'pickup_end' => $order->pickup_end,
                    'delivery_date' => $order->delivery_date,
                    'delivery_start' => $order->delivery_start,
                    'delivery_end' => $order->delivery_end,
                    'is_rated' => $order->is_rated,
                    'created_at' => date('Y-m-d H:i:s', strtotime($order->created_at)),
                    'address' => new ResourcesCustomerAddress($order->CustomerAddress),
                    'details' => $orderDetails,
                    'order_preference' => $orderPreference,
                    'delivery_raw_end' => $order->delivery_date . ' ' . $order->delivery_end,
                    'pickup_raw_start' => $order->pickup_date . ' ' . $order->pickup_start,
                    'cancellation_fee' => $cancellation,
                    'cancellation_buffer' => $cancellationBuffer,
                    'pickupRescStartBuffer' => $pickupStartBuffer->value,
                    'deliveryRescEndBuffer' => $pickupEndBuffer->value,
                    'rescheduling_fee' => $rescheduling_fee->value
                );
                $success_arr = array(
                    'status' => 1,
                    'message' => 'Successfully retrieved order details.',
                    'order' => $order,
                );

                return json_encode($success_arr, JSON_NUMERIC_CHECK);
            } else {
                $failure_arr = array(
                    'status' => 0,
                    'message' => 'No Order found against this ID.',
                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
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

    //====================== Helper ==========================

    public function cancelOrder(Request $request)
    {
        $request = json_decode($request->getContent(), true);

        // $validatedData = Validator::make($request->json()->all(), [
        //     'id'=>'required|numeric'
        // ]);

        $orderId = $request['id'];
        if (!is_numeric($orderId)) {
            $this->handelReturnResultFail("no action");
        }
        $order = Order::find($orderId);
        $order->canceled_at =  Carbon::now()->toDateTimeString();
        $order->promotion_id = NULL;
        $order->save();
        $success_arr = array(
            "status" => 1,
            "message" => "Order has been cancelled."
        );
        return json_encode($success_arr, JSON_NUMERIC_CHECK);
    }

    public function handelReturnResultFail($msg)
    {
        $failure_array = array(
            "status" => 0,
            "message" => $msg
        );
        return json_encode($failure_array, JSON_NUMERIC_CHECK);
    }


    public function rateOrder(Request $request)
    {
        if (!empty($request['customer_id']) && isset($request['customer_id'])) {
            $customerId = $request['customer_id'];
        } else {
            $failure_array = array(
                "status" => 0,
                "message" => "Parameter missing - customer_id should not be empty."
            );
            return json_encode($failure_array, JSON_NUMERIC_CHECK);
        }
        if (!empty($request['order_id']) && isset($request['order_id'])) {
            $orderId = $request['order_id'];
            $order = Order::select('id')->where('id', $orderId)->first();
            if (empty($order)) {
                $failure_array = array(
                    "status" => 0,
                    'message' => 'No Order found against this ID.',
                );
                return json_encode($failure_array, JSON_NUMERIC_CHECK);
            }
        } else {
            $failure_array = array(
                "status" => 0,
                "message" => "Parameter missing - order_id should not be empty."
            );
            return json_encode($failure_array, JSON_NUMERIC_CHECK);
        }
        if (!empty($request['customer_id']) && isset($request['customer_id'])) {
            $customerId = $request['customer_id'];
            $customer = Customer::select('id')->where('id', $customerId)->first();
            if (empty($customer)) {
                $failure_array = array(
                    "status" => 0,
                    'message' => 'No Customer found against this ID.',
                );
                return json_encode($failure_array, JSON_NUMERIC_CHECK);
            }
        } else {
            $failure_array = array(
                "status" => 0,
                "message" => "Parameter missing - customer_id should not be empty."
            );
            return json_encode($failure_array, JSON_NUMERIC_CHECK);
        }
        if (!empty($request['rating']) && isset($request['rating'])) {
            $rating = $request['rating'];
            if (is_numeric($rating)) {
            } else {
                $failure_array = array(
                    "status" => 0,
                    "message" => "Parameter rating should be numeric."
                );
                return json_encode($failure_array, JSON_NUMERIC_CHECK);
            }
        } else {
            $failure_array = array(
                "status" => 0,
                "message" => "Parameter missing - rating should not be empty."
            );
            return json_encode($failure_array, JSON_NUMERIC_CHECK);
        }


        // =======================validation===============================
        $validatedData = $request->validate([
            'order_id' => 'required|numeric',
            'customer_id' => 'required|numeric',
            'rating' => 'required|numeric',
            'comment' => 'required|string|max:200',
        ]);
        // =======================validation===============================


        $customerOrder = Order::select('id')
            ->where('id', $orderId)
            ->where('customer_id', $customerId)
            ->first();
        if (empty($customerOrder)) {
            $failure_arr = array(
                'status' => 0,
                'message' => 'No Such Order exists against this customer.',
            );
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
        } else {
            $customerOrder->rating = $rating;
            $customerOrder->is_rated = 1;
            if (isset($request['comment']) && !empty($request['comment'])) {
                $customerOrder->comments = $request['comment'];
            }
            $customerOrder->update();
            $success_arr = array(
                'status' => 1,
                'message' => 'Order rating has been saved successfully',
            );
            return json_encode($success_arr, JSON_NUMERIC_CHECK);
        }
    }
    public function changeOrderStatus()
    {
        // $deliveryFee = GlobalSetting::select('value')->where('setting_name', 'delivery_fee')->first();
        // $mints = env('TIME_FOR_DELIVERED');
        // $timeadded = $mints * 100;
        // $qur = "Update orders set status_id=3  where ADDTIME(delivery_date,delivery_end+" . $timeadded . ") < CURRENT_TIMESTAMP and status_id=5";
        // $results = DB::select($qur);

        $success_msg = array(
            "status" => 1,
            "message" => "Order status changed successfully.",
        );
        return json_encode($success_msg);
    }

    public function reschedule(Request $request)
    {
        $request = json_decode($request->getContent(), true);

        // =======================validation===============================
        $validatedData = Validator::make($request, [
            'order_id' => 'required|numeric',
            // 'pickup_date' => 'date',
            // 'pickup_start' => 'date_format:H:i:s',
            // 'pickup_end' => 'date_format:H:i:s',
            'delivery_date' => 'required|date',
            'delivery_start' => 'required|date_format:H:i:s',
            'delivery_end' => 'required|date_format:H:i:s',
            'reschedule_fee' => 'required|numeric|between:0,9999.99',
        ]);
        // =======================validation===============================
        if (!$validatedData->passes()) {
            return response()->json($validatedData->errors()->all(), 402);
        }
        $order = Order::find($request["order_id"]);
        if (!is_null($request["pickup_date"])) {
            $order->pickup_date = $request["pickup_date"];
            $order->pickup_start = $request["pickup_start"];
            $order->pickup_end = $request["pickup_end"];
        }
        $order->delivery_date = $request["delivery_date"];
        $order->delivery_start = $request["delivery_start"];
        $order->delivery_end = $request["delivery_end"];
        $order->reschedule_fee = $order->reschedule_fee + $request["reschedule_fee"];
        $order->update();
        $success_arr = array(
            "status" => 1,
            "message" => "Order has been rescheduled."
        );

        $notify = 'order #' . $order->id . ' rescheduled';
        \App\Http\Helper\utilitiesHelper::slack_notify(env('OPSXTECH_CHANEL'), $notify);
        return json_encode($success_arr, JSON_NUMERIC_CHECK);
    }

    public function ordersrepoadv(Request $request)
    {
        $Orders = Order::with('Promotion', 'OrderProducts', 'OrderProducts.ProductPrice', 'OrderProducts.ProductPrice.Product');
        if ($request['vfrom_val'] != null) {
            $Orders->whereBetween('pickup_date',  [$request['vfrom_val'] . " 00:00:00",  $request['vto_val'] . " 23:59:59"]);
        }
        if ($request['vcfrom_val'] != null) {
            $Orders->whereBetween('created_at',  [$request['vcfrom_val'] . " 00:00:00",  $request['vcto_val'] . " 23:59:59"]);
        }
        if ($request['customer_id_val'] != null) {
            $Orders->where('customer_id', $request['customer_id_val']);
        }
        $Orders->whereNull('canceled_at');
        $order_data =  $Orders->get();
        return response()->json(["success" => "1", "data" => reportOrdersResource::collection($order_data)], 200);
    }


    public function ordersmarket(Request $request)
    {
        $s_date = $request->sdate;
        $e_date = $request->edate;

        // $qur = "CALL market('" . $s_date . "','" . $e_date . "')";
        $qur = "SELECT 'new' stats,COUNT(orders_count) orders_count, IFNULL(sum(gross_sales),0) gross_sales,lk.city ,pickup_date FROM ( SELECT (SELECT COUNT(o1.id) FROM orders o1 WHERE customer_id = o.customer_id ) orders_count ,
        (SELECT sum(pp.price) FROM orders o3 
        LEFT JOIN order_products op  ON op.order_id=o3.id
      LEFT JOIN product_price pp  ON op.product_price_id=pp.id
         WHERE o3.id = o.id ) AS gross_sales
        ,o.customer_id,o.pickup_date
        ,l.city
                   FROM  orders o
        LEFT JOIN customer_address ca ON(ca.id=o.address_id)
                 LEFT JOIN locations l ON(l.id=ca.location_id)
                   WHERE date(o.pickup_date) BETWEEN '" . $s_date . "' AND '" . $e_date . "' 
                   AND o.canceled_at IS NULL 
      GROUP BY o.customer_id,o.pickup_date,l.city) lk  where orders_count=1 GROUP BY  lk.city,lk.pickup_date 
      UNION
      SELECT 'returning' status,COUNT(orders_count) orders_count, IFNULL(sum(gross_sales),0) gross_sales,lk.city ,pickup_date FROM ( SELECT (SELECT COUNT(o1.id) FROM orders o1 WHERE customer_id = o.customer_id ) orders_count ,
        (SELECT sum(pp.price) FROM orders o3 
        LEFT JOIN order_products op  ON op.order_id=o3.id
      LEFT JOIN product_price pp  ON op.product_price_id=pp.id
         WHERE o3.id = o.id ) AS gross_sales
        ,o.customer_id,o.pickup_date
        ,l.city
                   FROM  orders o
        LEFT JOIN customer_address ca ON(ca.id=o.address_id)
                 LEFT JOIN locations l ON(l.id=ca.location_id)
                   WHERE date(o.pickup_date) BETWEEN '" . $s_date . "' AND '" . $e_date . "' 
                   AND o.canceled_at IS NULL 
      GROUP BY o.customer_id,o.pickup_date,l.city) lk   where orders_count>1 GROUP BY  lk.city,lk.pickup_date";
        $res =  DB::select("SET sql_mode = ''");
        $res =  DB::select("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        $res =  DB::select($qur);
        return response()->json(["data" => $res], 200);



        //     SELECT COUNT((SELECT  COUNT(id) FROM orders o1 WHERE customer_id = o.customer_id HAVING COUNT(o1.id)=1)) New ,
        //     COUNT((SELECT  COUNT(id) FROM orders o2 WHERE customer_id = o.customer_id HAVING COUNT(o2.id)>1))Returning
        //     ,SUM((SELECT  sum(pp.price) FROM orders o3 
        //      LEFT JOIN order_products op  ON op.order_id=o3.id
        //       LEFT JOIN product_price pp  ON op.product_price_id=pp.id
        //      WHERE o3.id = o.id))+SUM(o.delivery_fee)+SUM(o.reschedule_fee)-SUM(o.promotion_discount)-SUM(o.discount_amount) gross_sales
        //    ,l.city,'2020/11/18' ddate
        //      FROM orders o 
        //      LEFT JOIN customer_address ca ON(ca.id=o.address_id)
        //      LEFT JOIN locations l ON(l.id=ca.location_id)
        //      WHERE pickup_date = '2020/11/18' 
        //      AND canceled_at IS NULL
        //     GROUP BY ca.location_id;
        //      SELECT sum(pp.price) FROM orders o 
        //        LEFT JOIN customer_address ca ON(ca.id=o.address_id)
        //        LEFT JOIN order_products op  ON(o.id=op.order_id)
        //        LEFT JOIN product_price pp   ON(pp.id=op.product_price_id)
        //        WHERE pickup_date = '2020/11/18' AND ca.location_id=26

    }

    // agent-app
    public function ordersAgent(Request $request)
    {
        $agentOrders =  Order::whereNull('confirm_prepare_route')->whereNull('canceled_at')
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('delivery_date', date('Y/m/d'))
                        ->where('status_id', 5);
                })->orWhere(function ($q) {
                    $q->where('pickup_date', date('Y/m/d'))
                        ->Where('status_id', 1);
                });
            })
            ->orderBy("id", "desc")
            ->limit(500)
            ->get();

        return AgentOrdersResource::collection($agentOrders);
    }

    public function ordersAgentDetails(Request $request)
    {
        $order = Order::with('OrderBag')->find($request["order_id"]);
        // return $order;
        return new AgentOrderDetails($order);
    }

    public static function withdrawalJob($oid, $minit)
    {
        withdrawalJob::dispatch($oid)->delay(now()->addMinutes($minit));
    }



    public function endRecept($id, Request $request)
    {
        $order = Order::find($id);
        $order->agent_note = $request->agent_note;
        $order->status_id = 7;
        $order->update();
        // Notifiy customer
        $title = "Order Pickup";
        $dt = Carbon::now();
        $body = "Great news, your Makwa has been picked up by " . auth()->user()->name . " at " . $dt->format('h:i');
        $noti = new AfterPickup($order->Customer->first_name, $title, $body);
        NotifyGetaway::sendNotification($order->Customer, $noti, $body, "orderDetails", $id, $title);
        return ResponseFormat::sucsess($order);
    }
}