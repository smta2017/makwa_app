<?php

namespace App\Http\Controllers\Api\Order;

use App\Bag as AppBag;
use App\Customer;
use App\GlobalSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helper\utilitiesHelper;
use App\Http\Resources\Order\OrderProductApiInvoiceResource;
use App\Http\Resources\Order\OrderProductApiResource;
use App\Http\Resources\Order\OrderProductResource;
use App\Http\Resources\OrderProduct\OrderProductResource as OrderProductOrderProductResource;
use App\Http\Resources\OrderProduct_invoice;
use App\Http\Resources\V1\bag\bag;
use App\Http\Resources\V1\Order\OrderResource;
use App\Http\Resources\Vendor\VendorOrdersResource;
use App\Jobs\withdrawalJob;
use App\Lib\AppGainGetaway;
use App\Lib\NotifyGetaway;
use App\Lib\ResponseFormat;
use App\Notifications\PickupSort;
use App\Order;
use App\OrderProductImage;
use App\OrderProduct;
use App\OrderService;
use App\OrderBag;
use App\ProductPrice;
use App\Repositories\Bag\BagRepository;
use App\Repositories\helpers\GeneralHelperRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\OrderProduct\OrderProductRepository;
use App\Service;
use Carbon\Carbon;
use Mail;

class OrderFacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOrderDetail(Request $request)
    {
        $order = Order::find($request->order_id);
        return [
            "order" => [
                "id" => $order->id,
                "delivery_date" => $order->delivery_date,
                "delivery_time" => Carbon::parse($order->delivery_start)->format('h:i a') . "-" . Carbon::parse($order->delivery_end)->format('h:i a'),
                "pickup_date" => $order->pickup_date
            ],
            "customer" => [
                "id" => $order->Customer->id,
                "name" => $order->Customer->first_name . " " . $order->Customer->last_name,
            ],
        ];
    }

    public function getProducts(Request $request)
    {

        $product_price = ProductPrice::where("service_id", $request->service_id)->whereNull('is_archive')->orderBy('id')->get();
        $product_price =  OrderProductResource::collection($product_price);

        $orders = Order::find($request->order_id);
        return response()->json(["success" => 1, "product" => $product_price, "order" => $orders], 200);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOrderProducts(OrderProductRepository $orderProductRepository, Request $request)
    {

        //sameh pickup sort

        $OrderBag = OrderBag::find($request->order_bag_id);
        // $OrderProducts = \App\OrderProduct::where('order_bag_id', $request->order_bag_id)->get();
        // $OrderProducts = $orderProductRepository::getProductsByBag($request->order_bag_id);
        // $OrderProducts = $orderProductRepository::getOrderProductsByService($OrderBag->order_id, $OrderBag->Bag->service_id);

        $fold = [];
        $hang = [];
        if ($request->opr == "delivery") {
            $OrderProducts = $orderProductRepository::getOrderProductsByService($OrderBag->order_id, $OrderBag->Bag->service_id);
            foreach ($OrderProducts as $OrderProduct) {
                if (\App\Http\Helper\preferenceHelper::getProductPreference($OrderProduct->id) == "F") {
                    array_push(
                        $fold,
                        [
                            "prfrence" => "F",
                            "op_id" => $OrderProduct->id,
                            // "p_count" => $OrderProduct->p_count,
                            "pp_id" =>  $OrderProduct->pp_id,
                            "name" => $OrderProduct['ProductPrice']['Product']['name'],
                            "name_ar" => $OrderProduct['ProductPrice']['Product']['name_ar'],
                            "color" => $OrderProduct->item_color,
                            "delivery_sort" => $OrderProduct->delivery_sorted,
                            "order_id" => $OrderProduct->order_id,
                            "main_image" => $OrderProduct->OrderProductImage->where('image_type', 'main')->first()['image_name'],
                        ]
                    );
                } elseif (\App\Http\Helper\preferenceHelper::getProductPreference($OrderProduct->id) == "H") {
                    array_push(
                        $hang,
                        [
                            "prfrence" => "H",
                            "op_id" => $OrderProduct->id,
                            // "p_count" => $OrderProduct->p_count,
                            "pp_id" =>  $OrderProduct->pp_id,
                            "name" => $OrderProduct['ProductPrice']['Product']['name'],
                            "name_ar" => $OrderProduct['ProductPrice']['Product']['name_ar'],
                            "color" => $OrderProduct->item_color,
                            "delivery_sort" => $OrderProduct->delivery_sorted,
                            "order_id" => $OrderProduct->order_id,
                            "main_image" => $OrderProduct->OrderProductImage->where('image_type', 'main')->first()['image_name'],
                        ]
                    );
                }
            }
            $OrderProductsR = [
                "fold" => $fold,
                "hang" => $hang,
            ];
            return response()->json(["success" => 1,  "data" => $OrderProductsR], 200);
        } else {
            $OrderProducts = $orderProductRepository::getProductsByBag($request->order_bag_id);
            $OrderProductsR =  OrderProductApiResource::collection($OrderProducts);
        }
        $total_items = count($OrderProducts);

        return response()->json(["success" => 1, "total_items" => $total_items, "data" => $OrderProductsR], 200);
    }

    public function getItemDetails(Request $request)
    {
        return OrderProductImage::where('order_product_id', $request->oP_id)->get();
    }

    public function addOrderProduct(Request $request, GeneralHelperRepository $generalHelperRepository)
    {
        $other_images = $request['other_images'];





        $OrderProducts = new OrderProduct();
        $OrderProducts->order_id = $request->order_id;
        $OrderProducts->product_price_id = $request["pp_id"];
        // $OrderProducts->order_service_id = $order_service_id;
        $OrderProducts->item_color = $request["color"];
        $OrderProducts->order_bag_id = $request["order_bag_id"];
        $OrderProducts->save();


        $fullPName  = "sorting/main-" . $request['order_id'] . "-" . $OrderProducts->id . "-" . $request['pp_id'] . "-" .  date('H:i') . ".png";
        $uploadMin = $generalHelperRepository->s3uploadImageBase64('s3-order', $request['main_image'], $fullPName);

        $OrderProductImage = new OrderProductImage();
        $OrderProductImage->order_product_id = $OrderProducts->id;
        $OrderProductImage->image_name =  $fullPName;
        $OrderProductImage->image_type = 'main';
        $OrderProductImage->save();


        if ($other_images) {
            foreach ($other_images as $index => $image) {
                $other_fullName = "sorting/" . $image['tag'] . "-" . $request['order_id'] . "-" . $request['pp_id'] . "-" .  date('H:i') . '-' . $index . ".png";
                $upload_other_image =  $generalHelperRepository->s3uploadImageBase64('s3-order', $image["img"], $other_fullName);
                if ($upload_other_image) {
                    $OrderProductImage = new OrderProductImage();
                    $OrderProductImage->order_product_id = $OrderProducts->id;
                    $OrderProductImage->image_name =  $other_fullName;
                    $OrderProductImage->image_type = $image['tag'];
                    $OrderProductImage->save();
                }
            }
        }
    }

    public function deleteOrderProduct(Request $request)
    {
        $validatedData = $request->validate([
            'pid' => 'required|numeric',
        ]);
        OrderProduct::destroy($request->pid);

        return response()->json(["success" => 1, "message" => "product removed"], 200);
    }

    public function confirmPickup(Request $request)
    {
        // $order_service_id = OrderService::where("service_id", $request->service_id)->where("order_id", $request->order_id)->first();
        // $order_service_id->pickup_sorted =  date('Y-m-d H:i:s');
        // $order_service_id->update();

        $OrderBag = OrderBag::find($request->order_bag_id);
        $OrderBag->pickup_sorted = date('Y-m-d H:i:s');
        $OrderBag->save();


        $order_id = $OrderBag->order_id;

        $order = Order::find($order_id);
        // $_pickuped = ::where('order_id', $request->order_id)->whereNull('pickup_sorted')->exists();

        $OrderService_pickuped = OrderBag::whereHas('Order', function ($query) use ($order_id) {
            $query->where('id', $order_id);
        })->whereNull('pickup_sorted')->exists();

        $customer = Customer::find($order->customer_id);

        if (!$OrderService_pickuped) {
            $order->status_id = 6;
            $deliveryFee = GlobalSetting::select('value')->where('setting_name', 'delivery_fee')->first();
            $order->delivery_fee = $deliveryFee->value;
            if (!is_null($order->nextday_fee)) {
                $nextday_fee = GlobalSetting::select('value')->where('setting_name', 'next_day_fee')->first();
                $order->nextday_fee = $nextday_fee->value;
            }
            $order->save();

            // if ($order->method_of_payment == "card" && $order->selected_card != null && \App::environment(['production'])) {
            withdrawalJob::dispatch($order->id)->delay(now()->addMinutes(env('WITHDRAWAL_TIME')));
            // } elseif ($order->method_of_payment == "cash") {
            //     $order_total_amount = OrderRepository::getOrderTotalAmount($order_id);
            //     $customer->forceWithdrawFloat($order_total_amount, ['description' => 'With#'.$order->id]);
            // }


            // Notify Customer
            $title = "Invoice Ready";
            $body =  $order->Customer->first_name . ", your order has been sorted and your detailed invoice is ready";
            $noti = new PickupSort($order->Customer->first_name, $title, $body);
            NotifyGetaway::sendNotification($order->Customer, $noti, $body, "orderDetails", $order_id, $title);

            $data = $this->orderDetails($order_id);
            if (\App::environment(['production'])) {
                Mail::send('emails.invoice',   $data, function ($m) use ($customer) {
                    $m->from('donotreply@makwaapp.com', 'Makwa');
                    $m->to($customer->email)->subject('Order Invoice');
                });
            }
            return response()->json(["success" => 1, "message" => "confirmed Pickup", "order_id" => $request->order_id], 200);
        } else {
            return response()->json(["success" => 1, "message" => "confirmed Pickup", "order_id" => $request->order_id], 200);
        }
    }


    public function getOrderDetailDelivery(OrderProductRepository $orderProductRepository, Request $request)
    {
        $order = OrderBag::find($request->order_bag_id)['Order'];
        $OrderBag = OrderBag::find($request->order_bag_id);
        // $OrderProducts = \App\OrderProduct::where('order_id', $OrderBag->order_id)->get();


        // $OrderBag = OrderBag::find($request->order_bag_id);
        // $OrderProducts = \App\OrderProduct::where('order_bag_id', $request->order_bag_id)->get();
        $OrderProducts = $orderProductRepository::getOrderProductsByService($OrderBag->order_id, $OrderBag->Bag->service_id);

        $fold = array();
        $hang = array();
        foreach ($OrderProducts as $OrderProduct) {
            if (\App\Http\Helper\preferenceHelper::getProductPreference($OrderProduct->id) == "F") {
                array_push(
                    $fold,
                    [
                        "prfrence" => "F",
                        "p_count" => $OrderProduct->p_count,
                        "pp_id" =>  $OrderProduct->pp_id,
                        "name" => $OrderProduct->name,
                        "order_id" => $OrderProduct->order_id
                    ]
                );
            } elseif (\App\Http\Helper\preferenceHelper::getProductPreference($OrderProduct->id) == "H") {
                array_push(
                    $hang,
                    [
                        "prfrence" => "H",
                        "p_count" => $OrderProduct->p_count,
                        "pp_id" =>  $OrderProduct->pp_id,
                        "name" => $OrderProduct->name,
                        "order_id" => $OrderProduct->order_id
                    ]
                );
            }
        }
        $OrderProductsR = [
            'service' => $OrderBag->Bag->Service->name,
            'order' => new VendorOrdersResource($order),
            'customer' => $order->Customer,
            "fold" => $fold,
            "hang" => $hang,
        ];
        return response()->json(["success" => 1,  "data" => $OrderProductsR], 200);
    }

    public function orderInvoice(Request $request)
    {
        $order_id = $request->order_id;
        $order_bag_id = $request->order_bag_id;
        return $this->orderDetailsByBag($order_bag_id);
    }

    public function orderDetailsByOrderId($order_id, OrderRepository $orderRepository, OrderProductRepository $orderProductRepository)
    {
        $order = Order::find($order_id);
        $orderProducts = OrderProductOrderProductResource::collection(orderProductRepository::getProductsByOrderId($order_id));
        $OrderProductsR = [
            'order' => new VendorOrdersResource($order),
            'customer' => $order->Customer,
            'products' => $orderProducts,
        ];
        return response()->json($OrderProductsR, 200);
    }

    public function orderDetailsByBag($order_bag_id)
    {

        $order_bag = OrderBag::find($order_bag_id);
        $service_id = $order_bag->Bag->Service->id;

        $order_id = $order_bag->Order["id"];

        $order = Order::find($order_id);
        // return $service_id;
        $OrderProducts = OrderProduct::select(
            \DB::raw('min(order_products.id) as op_id'),
            \DB::raw('count(product_price.id) as p_count'),
            "order_id",
            "product_price.id as pp_id",
            "products.name",
            "products.name_ar"
        )
            ->leftJoin('product_price', 'product_price.id', 'order_products.product_price_id')
            ->leftJoin('products', 'product_price.product_id', 'products.id')
            ->where('product_price.service_id', $service_id)
            ->where('order_bag_id', $order_bag->id)
            ->groupBy("product_price.id", "products.name", "products.name_ar", "order_id")
            ->get();
        $serviceDetail =  OrderProductApiInvoiceResource::collection($OrderProducts);
        $total_items = 0;

        foreach ($serviceDetail as $serviceDetai) {
            $total_items += $serviceDetai["p_count"];
        }


        $serviceName = Service::where("id", $service_id)->first("name");




        $subTotal = utilitiesHelper::orderSubTotal($order_id);

        $promoDiscount = utilitiesHelper::promotionCalculate($order->id, $subTotal);
        $vendorpromoDiscount = utilitiesHelper::vendorPromotionCalculate($order->id, $subTotal);
        $total_amount = utilitiesHelper::totalAmountCalculate($order->id, $subTotal, $promoDiscount + $vendorpromoDiscount);



        $serviceDetail = json_encode($serviceDetail, true);
        $serviceDetail = json_decode($serviceDetail, true);


        $bagRepository = new BagRepository();
        $serviceBags = $bagRepository::getOrderBagsByService($order_id, $service_id);


        $data = array(
            "id" => $order->id,
            'customer' => [
                "id" => $order->Customer["id"],
                "name" => $order->Customer["first_name"] . " " . $order->Customer["last_name"]
            ],
            'serviceName' => $serviceName["name"],
            'serviceDetail' => $serviceDetail,
            'sortedBags' => $serviceBags->whereNotNull('pickup_sorted'),
            'orderBags' => $serviceBags,
            'subTotal' => $subTotal,
            'totaItems' => $total_items,
            'discount' => 000,
            'cancellation_fee' => 000,
            'reschedule_fee' => 000,
            'deliveryFee' => 15,
            'promotionDiscount' => $promoDiscount,
            'makwa_discount' => 000,
            'qrcode' => $order_bag->Bag->qrcode,
            'total_amount' => $total_amount
        );

        return $data;
    }


    public function orderDetails($order_id)
    {
        $order = Order::find($order_id);

        $OrderProducts = $order->OrderProducts->groupBy('ProductPrice.service_id');
        foreach ($OrderProducts as $key => $val) {
            $serviceName[$key] = $val[0]->ProductPrice->Service->name;
            $serviceDetail[$key] = OrderProduct_invoice::collection($val->groupBy('product_price_id'));
        }

        $subTotal = utilitiesHelper::orderSubTotal($order_id);
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
            "order_id" => $order["id"],
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

        return $data;
    }



    public function confirmDeliverySort(Request $request)
    {
        $validatedData = $request->validate([
            'order_bag_id' => 'required|numeric',
        ]);
        $OrderBag1 = OrderBag::find($request->order_bag_id);

        $OrderBag = OrderBag::where("order_id", $OrderBag1->order_id)->whereHas('Bag', function ($q) use ($OrderBag1) {
            $q->where('service_id', $OrderBag1->Bag->service_id);
        })->update(['delivery_sorted' => date('Y-m-d H:i:s')]);

        $order_id = $OrderBag1->order_id;

        $OrderProduct_delivery = OrderBag::where('order_id', $order_id)->whereNull('delivery_sorted')->exists();

        if (!$OrderProduct_delivery) {
            $order = Order::find($order_id);
            $order->status_id = 5;
            if ($order->save()) {
                AppGainGetaway::sendNotification($order);
            }
        }


        return ResponseFormat::sucsess(['order_id' =>  $order_id], "confirmed");
    }

    public function fromagent(Request $request)
    {
        $validatedData = $request->validate([
            "ironbags" => 'required|numeric',
            "cleanironbags" => 'required|numeric',
        ]);



        if ($request['customCheck1'] == "true") {
            $orderService = new OrderService();
            $orderService->order_id = $request->order_id;
            $orderService->service_id = 1;
            $orderService->bag_count = $request['ironbags'];
            $orderService->save();
        }

        if ($request['customCheck2'] == "true") {
            $orderService = new OrderService();
            $orderService->order_id = $request->order_id;
            $orderService->service_id = 2;
            $orderService->bag_count = $request['cleanironbags'];
            $orderService->save();
        }

        if ($request['customCheck3'] == "true") {
            $orderService = new OrderService();
            $orderService->order_id = $request->order_id;
            $orderService->service_id = 4;
            $orderService->bag_count = $request['cleanfoldbags'];
            $orderService->save();
        }


        if (!empty($orderService)) {
            $order = order::find($request->order_id);
            $order->status_id = 2;
            $order->save();
        };
        return response()->json(["success" => 1, "message" => "confirmed from agent"], 200);
    }


    public function getOrderSumeryDetails(Request $request)
    {
        $order = Order::find($request->order_id);
        if (!empty($order)) {
            $OrderProducts = OrderProduct::select(\DB::raw('min(order_products.id) as op_id'), \DB::raw('count(product_price.id) as p_count'),  "order_id", "product_price.id as pp_id", "products.name")
                ->leftJoin('product_price', 'product_price.id', 'order_products.product_price_id')
                ->leftJoin('products', 'product_price.product_id', 'products.id')
                ->where('product_price.service_id', $request->servicetype)
                ->where('order_id', $order->id)
                ->groupBy("product_price.id", "products.name", "order_id")
                ->get();

            $fold = array();
            $hang = array();
            foreach ($OrderProducts as $OrderProduct) {
                if (\App\Http\Helper\preferenceHelper::getProductPreference($OrderProduct->op_id) == "F") {
                    array_push(
                        $fold,
                        [
                            "prfrence" => "F",
                            "p_count" => $OrderProduct->p_count,
                            "pp_id" =>  $OrderProduct->pp_id,
                            "name" => $OrderProduct->name,
                            "order_id" => $OrderProduct->order_id
                        ]
                    );
                } elseif (\App\Http\Helper\preferenceHelper::getProductPreference($OrderProduct->op_id) == "H") {
                    array_push(
                        $hang,
                        [
                            "prfrence" => "H",
                            "p_count" => $OrderProduct->p_count,
                            "pp_id" =>  $OrderProduct->pp_id,
                            "name" => $OrderProduct->name,
                            "order_id" => $OrderProduct->order_id
                        ]
                    );
                }
            }
            $OrderProductsR = [
                "fold" => $fold,
                "hang" => $hang,
            ];
            return response()->json(["success" => 1,  "data" => $OrderProductsR], 200);
        }
    }
}
