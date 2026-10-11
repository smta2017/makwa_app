<?php

namespace App\Repositories\Order;

use App\Gamify\Points\OrderCreated;
use App\Http\Helper\preferenceHelper;
use App\Http\Helper\utilitiesHelper;
use App\Http\Resources\ProductDetail;
use App\Http\Resources\OrderFastPreference;
use App\Http\Resources\V1\Customer\Customer;
use App\Http\Resources\V1\CustomerAddress\CustomerAddress;
use App\Http\Resources\V1\Location\LocationResource;
use App\Http\Resources\V1\Order\OrderResource;
use App\Http\Resources\V1\Preference\OtherPreference;
use App\Http\Resources\V1\Preference\Preference;
use App\Http\Resources\V1\Service\ServiceCategoryResource;
use App\Http\Resources\V1\ServiceTag\ServiceTagResource;
use App\Order;
use App\Repositories\Preference\PreferenceRepository;
use App\Service;
use Carbon\Carbon;

class OrderRepository
{
    public static function store($request)
    {
        $order = Order::create($request);
        // auth()->user()->givePoint(new OrderCreated($order));
        return $order;
    }


    public static function update($request, $id)
    {
        $order = Order::find($id);
        $order->update($request);
        return $order;
    }

    public static function show($id)
    {
        $order = Order::noneCanceled()->find($id);
        if (is_null($order)) {
            return 0;
        }

        // $subTotal = $order->OrderProducts->sum('ProductPrice.price');
        $subTotal = utilitiesHelper::orderSubTotal($order->id);
        $promoDiscount = utilitiesHelper::promotionCalculate($order->id, $subTotal);
        $vendorpromoDiscount = utilitiesHelper::vendorPromotionCalculate($order->id, $subTotal);


        $expressFee = utilitiesHelper::expressFee($order->id, $subTotal);

        $totalAmount = utilitiesHelper::totalAmountCalculate($order->id, $subTotal, $promoDiscount + $vendorpromoDiscount);

        $OrderProducts = $order->OrderProducts->groupBy('ProductPrice.service_id');
        $orderPreference = OrderFastPreference::collection($order->OrderFastPreferencies);

        $otherPref = OtherPreference::collection(PreferenceRepository::getGeneralOrderPreference($order->id));
        $preference = Preference::collection(PreferenceRepository::getOrderAvailable($order->id));


        $vendorServices = $vendorLocations = [];
        $helper = new utilitiesHelper();
        $vss = $order['vendor']->VendorServices;
        foreach ($vss as $value) {
            array_push($vendorServices, $value->Service);
        }

        foreach ($order->vendor->vendorLocations as $vendorlocation) {
            array_push($vendorLocations, $vendorlocation->Location);
        }

        $details = [];


        if ($order["promotion_discount"]) {
            array_push(
                $details,
                [
                    'title' => "Promotion discount",
                    'value' => $order["promotion_discount"],
                ]
            );
        }

        if ($expressFee) {
            array_push(
                $details,
                [
                    'title' => "Express fee",
                    'value' => $expressFee,
                ]
            );
        }
        if ($order["delivery_fee"]) {
            array_push(
                $details,
                [
                    'title' => "Delivery fee",
                    'value' => $order["delivery_fee"],
                ]
            );
        }
        $customer_address=[
            "id"=> null,
            "latitude"=> null,
            "longitude"=> null,
            "location_id"=> null,
            "location_name"=> null,
            "building_no"=> null,
            "street_address"=> null,
            "floor_no"=> null,
            "apartment_no"=> null,
            "type"=> null,
            "additional_directions"=> null,
            "is_default"=> null,
            "country_code"=> null,
            "phone_number"=> null,
            "landline"=> null,
            "building_type"=> null,
        ];
        if ($order["CustomerAddress"] != null) {

            $customer_address = new CustomerAddress($order["CustomerAddress"]);
        }

        return [
            'can_cancel' => true,
            'can_reschedule' => true,
            "order" => new OrderResource($order),
            "preference" => ["products" => $preference, "general" => $otherPref],
            'details' => ProductDetail::collection($OrderProducts),
            "customer" => new Customer($order["Customer"]),
            'address' => $customer_address,
            'review' => ($order->Reviews->count()) ? $order->Reviews[0] : [],
            'is_express' => $order->is_express,
            "vendor" => [
                "id" => $order['vendor']["id"],
                "name" => $order['vendor']["name"],
                "icon" => $order['vendor']["icon"],
                "locations" =>  LocationResource::collection($vendorLocations),
                "service_categories" => ServiceCategoryResource::collection($vendorServices),
                'service_tags' =>  ServiceTagResource::collection($order['vendor']->VendorServiceTags),

            ],
            'summery' => [
                'details' => $details,
                'subTotal' => $subTotal,
                'total_amount' => $totalAmount +  $expressFee,
            ],
        ];
    }

    public static function getOrdersSlotCount($location_id, $slotDate, $slotStart, $slotEnd)
    {
        return  Order::where(function ($q) use ($location_id, $slotDate, $slotStart, $slotEnd) {
            $q->where('pickup_date', $slotDate);
            $q->whereNull('canceled_at');
            $q->where(function ($q) use ($slotStart, $slotEnd) {
                $q->where('pickup_start', $slotStart);
                $q->where('pickup_end', $slotEnd);
            });
            $q->whereHas('CustomerAddress', function ($q) use ($location_id) {
                $q->where('location_id', $location_id);
            });
        })
            ->orWhere(function ($q) use ($location_id, $slotDate, $slotStart, $slotEnd) {
                $q->where('delivery_date', $slotDate);
                $q->whereNull('canceled_at');
                $q->where(function ($q)  use ($slotStart, $slotEnd) {
                    $q->where('delivery_start', $slotStart);
                    $q->where('delivery_end', $slotEnd);
                });
                $q->whereHas('CustomerAddress', function ($q) use ($location_id) {
                    $q->where('location_id', $location_id);
                });
            })->count();
    }

    public static function cancelOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->canceled_at =  Carbon::now()->toDateTimeString();
        $order->promotion_id = NULL;
        $order->save();
        return $order;
    }

    public static function getPromoCount($customerId, $promotionId)
    {
        $orderPromoCount = Order::where('customer_id', $customerId)->where('promotion_id', $promotionId)->count('id');
        return $orderPromoCount;
    }

    public static function getOrderInfo($id)
    {
        $order = Order::with('Customer')->where("id", $id)->first();
        return $order;
    }

    public static function getOrderTotalAmount($order_id)
    {
        $order = Order::find($order_id);

        // $subTotal = $order->OrderProducts->sum('ProductPrice.price');
        $subTotal = utilitiesHelper::orderSubTotal($order_id);
        $promoDiscount = utilitiesHelper::promotionCalculate($order->id, $subTotal);
        $vendorpromoDiscount = utilitiesHelper::vendorPromotionCalculate($order->id, $subTotal);
        $totalAmount = utilitiesHelper::totalAmountCalculate($order->id, $subTotal, $promoDiscount + $vendorpromoDiscount);

        return $totalAmount;
    }
    public static function getScanedOrderBags($order_id)
    {
        $order = Order::where('id', $order_id)->first();
        if ($order->status_id != 1) {
            return 'recepted';
        }
        $bags = [];

        // foreach ($orderBags->OrderBags as $bag) {
        foreach ($order->OrderBags as $OrdereBag) {
            $bags[] = [
                'id' =>  $OrdereBag->Bag['id'],
                'qrcode' =>  $OrdereBag->Bag['qrcode'],
                'service_id' =>  $OrdereBag->Bag['service_id'],
                'customer_id' =>  $OrdereBag->Bag['customer_id'],
            ];
        }
        // }
        return $bags;
    }
    public static function getReusableBags($order_id)
    {
        $order = Order::find($order_id);
        $bags = [];
        $reusableBags = $order->Customer->Bags;
        foreach ($reusableBags  as $customereBag) {
            $bags[] = [
                'id' =>  $customereBag['id'],
                'qrcode' =>  $customereBag['qrcode'],
                'service_id' =>  $customereBag['service_id'],
                'customer_id' =>  $customereBag['customer_id'],
            ];
        }
        $kk = "";
        return $bags;
    }


    public static function getOrderServices($order_id)
    {
        return Service::whereHas('Bags.OrderBags', function ($q) use ($order_id) {
            $q->where('order_id', $order_id);
        })->get();
    }
}
