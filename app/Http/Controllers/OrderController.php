<?php

namespace App\Http\Controllers;

use App\Customer;
use App\GlobalSetting;
use App\Http\Controllers\Api\Bag\BagController;
use App\Http\Controllers\Api\Helper\OrderHelperController;
use App\Http\Controllers\payment2\paymentController;
use App\Http\Resources\OrderProduct_invoice;
use App\Jobs\withdrawalJob;
use App\Order;
use App\OrderProduct;
use App\OrderService;
use App\ProductPrice;
use Illuminate\Http\Request;
use Mail;
use DB;
use Auth;
use App\Http\Helper\utilitiesHelper;
use App\Http\Resources\OrderProduct\OrderProductResource;
use App\Repositories\Order\OrderRepository;
use App\requestLogs;
use App\Http\Resources\V1\Order\OrderDetails;
use App\Lib\GlobalSettings;
use App\Repositories\OrderProduct\OrderProductRepository;
use App\Repositories\TimeSlot\TimeSlotRepository;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['getProductsDetails']]);
    }

    public function index()
    {
        $admin_location = Auth::user()->AdminLocations->pluck('location_id');
        $orders = Order::orderBy('id', 'Desc')->paginate(100);

        if ($admin_location->count() != 0) {
            $orders = $orders->whereIn("CustomerAddress.location_id", $admin_location);
        }
        $qur = "SELECT start ,end,l.city, COUNT(tot_sel.id) cout FROM (SELECT     o.id, o.pickup_start start,o.pickup_end end,o.pickup_date the_date,ca.location_id ,'pickup'
                FROM orders o 
                LEFT JOIN customer_address ca ON (o.address_id=ca.id)
                WHERE (o.pickup_date = '" . date("y-m-d") . "')  and o.canceled_at is NULL
                UNION
                SELECT     o2.id,o2.delivery_start start,o2.delivery_end end,o2.delivery_date the_date,ca.location_id  ,'delivery'
                FROM orders o2
                LEFT JOIN customer_address ca ON (o2.address_id=ca.id)
                WHERE (o2.delivery_date = '" . date("y-m-d") . "')   and o2.canceled_at is NULL) tot_sel
                LEFT JOIN locations l ON (l.id=tot_sel.location_id)
                GROUP BY tot_sel.start, tot_sel.end  ,tot_sel.location_id,l.city order by city";


        $orders_slots = DB::select($qur);
        // $orders_slot_zayed = DB::select($qur2);

        $thedate = "0";
        return view('admin.order.index', compact('orders', 'thedate', 'orders_slots'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.order.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $order = new Order();
        $order->customer_id = $request->customer_id;
        $order->address_id = $request->customer_address;
        $order->pickup_date = $request->pickup_date;
        $order->pickup_start = $request->pickup_start;
        $order->pickup_end = $request->pickup_end;
        $order->delivery_date = $request->delivery_date;
        $order->delivery_start = $request->delivery_start;
        $order->delivery_end = $request->delivery_end;
        $order->status_id = 1;
        $order->created_by = Auth::user()->name;
        //-------
        $order->delivery_fee = "0";
        $order->method_of_payment = "cash";
        $order->vendor_id = OrderHelperController::orderRoutingVendor($order);

        $order->save();
        return redirect("/orders");
    }

    public function show($id, OrderRepository $orderResource)
    {
        $order = Order::find($id);
        $orderProducts = [];

        $op = $order->OrderProducts;
        if ($op->count()) {
            if ($order->OrderProducts[0]->order_bag_id) {
                $orderServices = OrderRepository::getOrderServices($order->id);
                foreach ($orderServices as $key => $orderService) {
                    $products =  OrderProductRepository::getOrderProductsByService($order->id, $orderService->id);
                    array_push(
                        $orderProducts,
                        [
                            "service" => $orderService->name,
                            "products" => $products
                        ]
                    );
                }
            } else {
                $orderServices = Order::find($order->id)->OrderServices;
                // return $orderServices;
                foreach ($orderServices as $key => $orderService) {
                    $products =  OrderProduct::where('order_service_id', $orderService->id)->get();
                    array_push(
                        $orderProducts,
                        [
                            "service" => $orderService->Service->name,
                            "products" => $products
                        ]
                    );
                }
                return view('admin.order.showOld', compact('order', 'orderProducts'));
            }
        }
        return view('admin.order.show', compact('order', 'orderProducts'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order = Order::find($id);
        return view('admin.order.edit', compact('order'));
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
        $oo = $request->payment_method;

        $order = Order::find($id);
        $order->discount_amount = $request->discount;
        $order->delivery_fee = $request->delivery_fee;
        $order->nextday_fee = $request->nextday_fee;
        $order->promotion_id = $request->promotion_id;
        $order->pickup_date = $request->pickup_date;
        $order->pickup_start = $request->pickup_start;
        $order->pickup_end = $request->pickup_end;
        $order->delivery_date = $request->delivery_date;
        $order->delivery_start = $request->delivery_start;
        $order->delivery_end = $request->delivery_end;

        if (!$request->payment_method == null) {
            if ($request->payment_method == 'cash') {
                $order->method_of_payment = $request->payment_method;
                $order->selected_card = '';
            } else {
                $order->method_of_payment = 'card';
                $order->selected_card = $request->payment_method;
            }
        }
        $order->vendor_id = $request->vendor;
        $order->status_id = $request->order_status;
        $order->save();
        return redirect("/orders");

        // return $request;
    }


    public function destroy($id)
    {
        //cancel order
        $mytime = \Carbon\Carbon::now();
        $order = Order::find($id);
        $order->canceled_at = $mytime->toDateTimeString() . '_by_' . Auth::guard('admin')->user()->name;
        $order->promotion_id = NULL;
        $order->save();
        return redirect("/orders");
    }

    //======================================================
    public function ordersrepo(Request $request)
    {
        if ($request->isMethod('post')) {
            $wer = '';
            if ($request['place'] == 'created-date') {
                $wer = " WHERE date(o.created_at) Between '" . $request['vfrom'] . "' and '" . $request['vto'] . "'";
            } else if ($request['place'] == 'pickup-date') {
                $wer = " WHERE date(o.pickup_date) Between '" . $request['vfrom'] . "' and '" . $request['vto'] . "'";
            } else {
                $wer = " WHERE date(o.delivery_date) Between '" . $request['vfrom'] . "' and '" . $request['vto'] . "'";
            }

            $qur = "SELECT  SUM(pp.price) sub_total
                            ,count(op.id) pc
                            ,o.id
                            ,o.customer_id
                            ,o.delivery_fee
                            ,o.nextday_fee
                            ,o.reschedule_fee
                            ,o.service_fee
                            ,o.tax_amount
                            ,o.discount_amount
                            ,o.pickup_date
                            ,o.pickup_start
                            ,o.pickup_end
                            ,o.delivery_date
                            ,o.delivery_start
                            ,o.delivery_end
                            ,o.promotion_discount 
                            ,o.pickup_date
                            ,o.created_at
                            ,o.method_of_payment
                            ,prom.name as promotion
                            ,v.name as vendor
                            ,l.city as location_name
                            FROM orders o
                            LEFT JOIN order_products op ON (o.id=op.order_id)
                            LEFT JOIN product_price pp ON (op.product_price_id=pp.id)
                            LEFT JOIN promotions prom ON (o.promotion_id=prom.id)
                            LEFT JOIN vendors v ON (o.vendor_id=v.id)
                            LEFT JOIN customer_address ca ON (o.address_id=ca.id)
                            LEFT JOIN locations l ON (ca.location_id=l.id) " . $wer . " AND o.canceled_at IS NULL
                            GROUP BY o.id,op.order_id
                            ,o.delivery_fee
                            ,o.reschedule_fee
                            ,o.service_fee
                            ,o.tax_amount
                            ,o.discount_amount
                            ,o.pickup_date
                            ,o.pickup_start
                            ,o.pickup_end
                            ,o.nextday_fee
                            ,o.method_of_payment  
                            ,o.delivery_date
                            ,o.delivery_start
                            ,o.delivery_end
                            ,o.promotion_discount
                            ,o.pickup_date
                            ,o.customer_id
                            ,o.id
                            ,o.created_at
                            ,prom.name
                            ,v.name  
                            ,l.city ";
            $Orders = DB::select($qur);


            return view('admin.order.ordersrepo', compact('Orders'));
        }
        return view('admin.order.ordersrepo');
    }


    public function ordersrepoadv(Request $request)
    {
        return view('admin.order.ordersrepoadv');
    }

    public function ordersmarket(Request $request)
    {
        return view('admin.order.ordersmarket');
    }


    public function pickupOnDelovery(Request $request)
    {
        $order = Order::find($request->order_id);
        $customer_id = $order->customer_id;
        $address_id = $order->address_id;

        $request["pickup_slot"] = date('Y-m-d H:i:s');
        $request['location_id'] = $order->CustomerAddress["location_id"];
        $turnAroundTime = GlobalSettings::turnaroundTime();
        $deliverySlot = TimeSlotRepository::getDeliverySlots($request, 48)[0];
        $storeOrderRequest = [
            'vendor_id' => $order->vendor_id,
            'customer_id' => $customer_id,
            'address_id' => $address_id,
            'status_id' => 1,
            'pickup_date' => date('Y-m-d'),
            'pickup_start' => date('H:i'),
            'pickup_end' => date('H:i'),
            'created_by' => Auth::user()->name,
            'method_of_payment' => $order->method_of_payment,
            'selected_card' =>  $order->selected_card,
            'delivery_date' => $deliverySlot["date"],
            'delivery_start' => $deliverySlot["slots"][0]['start'],
            'delivery_end' => $deliverySlot["slots"][0]['end'],
        ];
        $newOrder =  OrderRepository::store($storeOrderRequest);
        return redirect("/agent/recept-order/" . $newOrder->id);
    }


    public function archiveorder($id)
    {
        //cancel order
        $mytime = \Carbon\Carbon::now();
        $order = Order::find($id);
        $order->canceled_at = $order->canceled_at . '_archive';
        $order->promotion_id = NULL;
        $order->save();
        return redirect("/orders");
    }

    public function vendorrepo(Request $request)
    {
        if ($request->isMethod('post')) {
            $rrf = $request['vendor_id'];
            $order_ids = OrderService::where('vendor_id', $rrf)->get('order_id');
            $qur = "SELECT
                    op.id
                    ,op.order_id
                    ,o.customer_id
                    ,o.pickup_date
                    ,COUNT(product_price_id) count
                    ,pp.price
                    ,s.name service_name
                    ,p.name product_name
                    FROM order_products op 
                    LEFT JOIN orders o ON(o.id=op.order_id)
                    LEFT JOIN product_price pp ON(pp.id=op.product_price_id)
                    LEFT JOIN order_services os ON (pp.service_id=os.service_id) AND (op.order_id=os.order_id)
                    LEFT JOIN services s ON(pp.service_id=s.id)
                    LEFT JOIN products p ON(pp.product_id=p.id)
                    WHERE  o.vendor_id=$rrf  and o.pickup_date Between '" . $request['vfrom'] . " 00:00:00' and '" . $request['vto'] . " 23:59:59' GROUP BY 
                    op.id ,op.order_id
                            ,op.product_price_id         
                            ,o.customer_id
                            ,pp.price
                            ,s.name
                            ,p.name
                            ,o.pickup_date;";
            $OrderProducts = DB::select($qur);
            return view('admin.order.vendorrepo', compact('OrderProducts'));
        }
        return view('admin.order.vendorrepo');
    }


    public function servicestep1(Request $request)
    {
        $validatedData = $request->validate([
            "ironbags" => 'required|numeric',
            "cleanironbags" => 'required|numeric',
        ]);

        if ($request['customCheck1'] == "on") {
            $orderService = new OrderService();
            $orderService->order_id = $request->order_id;
            $orderService->service_id = 1;
            $orderService->bag_count = $request['ironbags'];
            $orderService->save();
        }

        if ($request['customCheck2'] == "on") {
            $orderService = new OrderService();
            $orderService->order_id = $request->order_id;
            $orderService->service_id = 2;
            $orderService->bag_count = $request['cleanironbags'];
            $orderService->save();
        }
        if ($request['customCheck3'] == "on") {
            $orderService = new OrderService();
            $orderService->order_id = $request->order_id;
            $orderService->service_id = 4;
            $orderService->bag_count = $request['washfoldbags'];
            $orderService->save();
        }


        if (!empty($orderService)) {
            $order = order::find($request->order_id);
            $order->status_id = 2;
            $order->save();
        };
        return  redirect("/orders");
    }

    public function getServicestep2(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|numeric",
            "servicetype" => "numeric",
        ]);

        $order = order::find($request->id);


        if (!empty($order)) {
            if ($order->status_id != 2) {
                return "wrong step";
            } else {
                if ($request->has('servicetype')) {
                    $ProductPrices = ProductPrice::where('service_id', $request->servicetype)->whereNull('is_archive')->orderBy('id')->get();
                } else {
                    $ProductPrices = [];
                }
                $OrderProducts = OrderProduct::select(
                    "order_products.id",
                    "order_products.order_id",
                    "product_price.service_id",
                    "product_price.product_id",
                    "order_products.product_price_id",
                    "order_products.order_service_id",
                    "product_price.price",
                )->leftJoin('product_price', 'product_price.id', 'order_products.product_price_id')
                    ->where('product_price.service_id', $request->servicetype)
                    ->where('order_id', $order->id)->get();
                return view('admin.facility.pickupsort', compact('order', 'ProductPrices', 'OrderProducts'));
            }
        } else {
            return "wrong data";
        }
    }

    public function addProductOrderItem(Request $request)
    {
        $validatedData = $request->validate([
            'oid' => 'required|numeric',
            'ppid' => 'required|numeric',
        ]);

        //need to handling

        $OrderProduct = new OrderProduct();
        $OrderProduct->order_id = $request->oid;
        $OrderProduct->order_service_id = $request->oid;
        $OrderProduct->product_price_id = $request->ppid;
        $OrderProduct->save();
        return redirect('/service-step2?servicetype=' . $OrderProduct->ProductPrice->Service->id . '&id=' . $request->oid);
    }

    public function confirmPickupSort(Request $request)
    {
        $validatedData = $request->validate([
            'oid' => 'required|numeric',
            'sid' => 'required|numeric',
        ]);
        $order = Order::find($request->oid);
        $sid = $request->sid;
        $OrderService = OrderService::where('order_id', $request->oid)->where('service_id', $sid)->first();
        $OrderService->pickup_sorted = date('Y-m-d H:i:s');
        $OrderService->save();

        $OrderService_pickuped = OrderService::where('order_id', $request->oid)->whereNull('pickup_sorted')->exists();

        if (!$OrderService_pickuped) {
            $customer = Customer::find($order->customer_id);
            $OrderProducts = $order->OrderProducts->groupBy('ProductPrice.service_id');

            foreach ($OrderProducts as $key => $val) {
                $serviceName[$key] = $val[0]->ProductPrice->Service->name;
                $serviceDetail[$key] = OrderProduct_invoice::collection($val->groupBy('product_price_id'));
            }

            $subTotal = utilitiesHelper::orderSubTotal($request->oid);
            $promoDiscount = utilitiesHelper::promotionCalculate($order->id, $subTotal);
            $vendorpromoDiscount = utilitiesHelper::vendorPromotionCalculate($order->id, $subTotal);
            $total_amount = utilitiesHelper::totalAmountCalculate($order->id, $subTotal, $promoDiscount + $vendorpromoDiscount);

            $serviceDetail = json_encode($serviceDetail, true);
            $serviceDetail = json_decode($serviceDetail, true);

            $customerdata = [
                // Customer
                "first_name" => $order->Customer["first_name"],
                "last_name" => $order->Customer["last_name"],
                // address
                "building_no" => $order->CustomerAddress["building_no"],
                "street_address" => $order->CustomerAddress["street_address"],
                "floor_no" => $order->CustomerAddress["floor_no"],
                "apartment_no" => $order->CustomerAddress["apartment_no"],
                // order
                "pickup_date" => $order["pickup_date"],
                "pickup_start" => $order["pickup_start"],
                "pickup_end" => $order["pickup_end"],
                "delivery_date" => $order["delivery_date"],
                "delivery_start" => $order["delivery_start"],
                "delivery_end" => $order["delivery_end"],
                "promotion_discount" => $order["promotion_discount"],
            ];

            $order->status_id = 6;

            $deliveryFee = 0;
            $deliveryFee = GlobalSetting::select('value')->where('setting_name', 'delivery_fee')->first();
            $order->delivery_fee = $deliveryFee->value;

            $nextday_fee = 0;
            if (!is_null($order->nextday_fee)) {
                $nextday_fee = GlobalSetting::select('value')->where('setting_name', 'next_day_fee')->first()->value;
                $order->nextday_fee = $nextday_fee;
            }

            $data = array(
                'customer' => $customerdata,
                'serviceName' => $serviceName,
                'serviceDetail' => $serviceDetail,
                'subTotal' => $subTotal,
                'discount' => 000,
                'cancellation_fee' => 000,
                'reschedule_fee' => 000,
                'deliveryFee' => $deliveryFee->value,
                'next_day_fee' => $nextday_fee,
                'promotionDiscount' => $promoDiscount,
                'makwa_discount' => 000,
                'total_amount' => $total_amount
            );


            $order->save();
            if (\App::environment(['production'])) {
                if ($order->method_of_payment == "card" && $order->selected_card != null) {
                    withdrawalJob::dispatch($order->id)->delay(now()->addMinutes(env('WITHDRAWAL_TIME')));
                }
                if ($order->id != 4211) {
                    Mail::send('emails.invoice',   $data, function ($m) use ($customer) {
                        $m->from('donotreply@makwaapp.com', 'Makwa');

                        $m->to($customer->email)->subject('Order Invoice');
                    });
                }
            }
            return view('admin.facility.sortinvoice', compact('order', 'sid'));
        } else {
            return view('admin.facility.sortinvoice', compact('order', 'sid'));
        }
    }

    public function getServicestep3(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|numeric',
            'servicetype' => 'required|numeric',
        ]);

        $order = order::where('id', $request->id)->where('status_id', 6)->first();

        // return $order->OrderProducts->count();
        if (!empty($order)) {
            return view('admin.facility.deliverysort', compact('order'));
        }
    }

    public function confirmitem(Request $request)
    {
        $validatedData = $request->validate([
            'oid' => 'required|numeric',
            'product_price_id' => 'required|numeric'
        ]);

        $OrderProduct = OrderProduct::where('product_price_id', $request->product_price_id)
            ->where('order_id', $request->oid)
            ->update(['delivery_sorted' => '1']);
        return redirect('/service-step3?id=' . $request->oid);
    }

    public function confirmDeliverySort(Request $request)
    {
        $validatedData = $request->validate([
            'oid' => 'required|numeric',
            'sid' => 'required|numeric',
        ]);

        $OrderService = OrderService::where('order_id', $request->oid)->where('service_id', $request["sid"])->first();
        $OrderService->delivery_sorted = 1;
        $OrderService->save();

        $OrderService_delivery = OrderService::where('order_id', $request->oid)->whereNull('delivery_sorted')->exists();

        if (!$OrderService_delivery) {
            $orders = Order::find($request->oid);
            $orders->status_id = 5;
            $orders->save();
        }
        return redirect('/orders');
    }

    public function resetSort(Request $request)
    {
        $validatedData = $request->validate([
            'oid' => 'required|numeric',
            'pid' => 'required|numeric',
        ]);
        OrderProduct::where('product_price_id', $request->pid)->where('order_id', $request->oid)->delete();
        return redirect('/service-step2?servicetype=' . $request->svid . '&id=' . $request->oid);
    }

    public function searchOrders(Request $request)
    {
        $orders = "";
        if (isset($request->customer_id)) {
            $orders = Order::where('customer_id', $request->customer_id)->get();
        } elseif (isset($request->order_id)) {
            $orders = Order::where("id", $request->order_id)->get();
        }
        return view('admin.order.index', compact('orders'));
    }

    public function toVendors(Request $request)
    {
        $orders = Order::where('status_id', 4)->get();
        return view('admin.order.pending-vendors', compact('orders'));
    }

    public function toVendorsSave(Request $request)
    {
        $OrderService = OrderService::where('order_id', $request->oid)->where('service_id', $request["sid"])->first();
        $OrderService->vendor_id = $request->vendor_id;
        $OrderService->save();

        $OrderService_vendor = OrderService::where('order_id', $request->oid)->whereNull('vendor_id')->exists();

        if (!$OrderService_vendor) {
            $orders = Order::find($request->oid);
            $orders->status_id = 6;
            $orders->save();
        }

        return redirect('/to-vendors');
    }

    public function modefyProduct(Request $request)
    {
        $OrderProd = OrderProduct::find($request->proid);

        if ($request->operat == "inc") {
            $OrderProduct = new OrderProduct();
            $OrderProduct->order_id = $OrderProd->order_id;
            $OrderProduct->product_price_id = $OrderProd->product_price_id;
            $OrderProduct->order_service_id = $OrderProd->order_service_id;
            $OrderProduct->save();
        } elseif ($request->operat == "dec") {
            $OrderProduct = OrderProduct::destroy($request->proid);
        }
        return redirect('/orders' . "/" . $OrderProd->order_id);
    }

    public function resendinvoice(Request $request)
    {
        $order = Order::find($request->oid);
        $customer = Customer::find($order->customer_id);
        $orderChange = $order;
        $OrderProducts = $order->OrderProducts->groupBy('ProductPrice.service_id');
        $subTotal = 0;
        foreach ($OrderProducts as $key => $val) {
            $serviceName[$key] = $val[0]->ProductPrice->Service->name;
            $serviceDetail[$key] = OrderProduct_invoice::collection($val->groupBy('product_price_id'));
        }

        $subTotal = utilitiesHelper::orderSubTotal($request->oid);


        $promoDiscount = utilitiesHelper::promotionCalculate($order->id, $subTotal);
        $vendorpromoDiscount = utilitiesHelper::vendorPromotionCalculate($order->id, $subTotal);
        $total_amount = utilitiesHelper::totalAmountCalculate($order->id, $subTotal, $promoDiscount + $vendorpromoDiscount);


        $serviceDetail = json_encode($serviceDetail, true);
        $serviceDetail = json_decode($serviceDetail, true);

        $customerdata = [
            // Customer
            "first_name" => $order->Customer["first_name"],
            "last_name" => $order->Customer["last_name"],
            // address
            "building_no" => $order->CustomerAddress["building_no"],
            "street_address" => $order->CustomerAddress["street_address"],
            "floor_no" => $order->CustomerAddress["floor_no"],
            "apartment_no" => $order->CustomerAddress["apartment_no"],
            // order
            "pickup_date" => $order["pickup_date"],
            "pickup_start" => $order["pickup_start"],
            "pickup_end" => $order["pickup_end"],
            "delivery_date" => $order["delivery_date"],
            "delivery_start" => $order["delivery_start"],
            "delivery_end" => $order["delivery_end"],
            "promotion_discount" => $order["promotion_discount"],
        ];
        $deliveryFee = $order->delivery_fee;
        $nextday_fee = $order->nextday_fee;


        $data = array(
            'order' => $order,
            'customer' => $customerdata,
            'serviceName' => $serviceName,
            'serviceDetail' => $serviceDetail,
            'subTotal' => $subTotal,
            'discount' => 000,
            'cancellation_fee' => 000,
            'reschedule_fee' => 000,
            'deliveryFee' => $deliveryFee,
            'next_day_fee' => $nextday_fee,
            'promotionDiscount' => $promoDiscount,
            'makwa_discount' => $order->discount_amount,
            'total_amount' => $total_amount
        );

        Mail::send('emails.invoice',   $data, function ($m) use ($customer) {
            $m->from('donotreply@makwaapp.com', 'Makwa');
            $m->cc('mostafa_khaled3@hotmail.com');
            $m->bcc('smta2017@yahoo.com');
            $m->to($customer->email)->subject('Order Invoice');
        });
        Mail::send('emails.invoice',   $data, function ($m) {
            $m->from('donotreply@makwaapp.com', 'Makwa');
            $m->to('smta2017@yahoo.com')->subject('Order Invoice');
        });
        Mail::send('emails.invoice',   $data, function ($m) {
            $m->from('donotreply@makwaapp.com', 'Makwa');
            $m->to('makwainvoice@gmail.com')->subject('Order Invoice');
        });
        return redirect('/orders');
    }



    public function addnewproductitem(Request $request)
    {

        $sevice_id = ProductPrice::find($request->ppid)->service_id;
        // $OrderService_id =  OrderService::where("service_id", $sevice_id)->where("order_id", $request->oid)->first('id');

        // if ($OrderService_id == null) {
        //     $OrderService = new OrderService();
        //     $OrderService->order_id = $request->oid;
        //     $OrderService->service_id = $sevice_id;
        //     $OrderService->save();
        //     $OrderService_id = $OrderService->id;
        // } else {
        //     $OrderService_id = $OrderService_id["id"];
        // }

        $OrderProduct = new OrderProduct();
        $OrderProduct->order_id = $request->oid;
        $OrderProduct->product_price_id = $request->ppid;
        // $OrderProduct->order_service_id = $OrderService_id;
        $OrderProduct->save();

        return redirect('/orders' . "/" . $request->oid);
    }


    public static function withdrawalAmount($oid, $amount)
    {
        $withdrawal_amount = paymentController::withdrawalAmount($oid, $amount);
        if ($withdrawal_amount["success"] == "false") {
            $reval = "<h2 style='color:red'> Withdrawal not complete </h2>";
        } elseif ($withdrawal_amount["success"] == "true") {
            $reval = "<h2  style='color:green'> Withdrawal has been a success </h2>";
        }
        return $reval;
    }

    public static function withdrawal($oid)
    {
        $withdrawal = [];
        $order = Order::find($oid);
        if ($order->method_of_payment == "cash") {
            $order_total_amount = OrderRepository::getOrderTotalAmount($oid);
            $order->Customer->forceWithdrawFloat($order_total_amount, ['description' => 'With#' . $order->id]);
        } elseif ($order->method_of_payment == "card" && $order->selected_card != null && \App::environment(['production'])) {

            $msg = "";
            if ($order->buyed == null) {
                $withdrawal = paymentController::withdrawal($oid);
                if (array_key_exists("data.message", $withdrawal)) {
                    $msg = $withdrawal["data.message"];
                }
                $order->buyed = $withdrawal["success"] . "," . $withdrawal["pending"] . "," . ($withdrawal["amount_cents"] / 100) .  "," . date('Y-m-d H:i:s') .  "," . $msg;

                if ($withdrawal["success"] == "false") {
                    $order->method_of_payment = "cash";
                    $order_total_amount = OrderRepository::getOrderTotalAmount($order->id);
                    $order->Customer->forceWithdrawFloat($order_total_amount, ['description' => 'CC#' . $order->id]);
                }

                $order->save();

                $requestLog = new requestLogs();
                $requestLog->request = json_encode($withdrawal);
                $requestLog->save();
            } else {
                $buyed =  explode(",", $order->buyed);
                if ($buyed[0] == "false") {
                    $withdrawal = paymentController::withdrawal($oid);
                    if (array_key_exists("data.message", $withdrawal)) {
                        $msg = $withdrawal["data.message"];
                    }
                    $order->buyed = $withdrawal["success"] . "," . $withdrawal["pending"] . "," . ($withdrawal["amount_cents"] / 100) . "," . date('Y-m-d H:i:s') . "," . $msg;
                    $order->save();
                }
            }
            $reval = "";
            if ($withdrawal["success"] == "false") {
                $reval = "<h2 style='color:red'> Withdrawal not complete </h2>";
            } elseif ($withdrawal["success"] == "true") {
                $reval = "<h2  style='color:green'> Withdrawal has been a success </h2>";
            }
            return $reval;
        }
    }

    public function getOrdersAgent()
    {
        return view("admin.agent.index");
    }

    public function receptOrder($id, OrderRepository $orderRepository)
    {
        $orderInfo = $orderRepository::getOrderInfo($id);
        return view("admin.agent.receptOrder", compact('orderInfo'));
    }

    public function deliveryOrder($id, OrderRepository $orderRepository)
    {
        $order = Order::find($id);
        $customer = $order->Customer;
        $orderInfo = json_encode($customer);
        $orderInfo = json_decode($orderInfo, true);
        return view('admin.agent.deleverOrder', compact('customer', 'order'));
    }

    public function getProductsDetails($id)
    {
        $order = Order::find($id);
        $orderProducts_iron = OrderProductRepository::getOrderProductsImagesByService($id, 1);
        $orderProducts_clean = OrderProductRepository::getOrderProductsImagesByService($id, 2);
        return view('product_details', compact("order", "orderProducts_iron", "orderProducts_clean"));
    }
}
