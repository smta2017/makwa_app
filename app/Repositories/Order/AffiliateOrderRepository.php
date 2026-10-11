<?php

namespace App\Repositories\Order;

use App\Http\Helper\utilitiesHelper;
use App\Http\Resources\OrderFastPreference;
use App\Http\Resources\ProductDetail;
use App\Http\Resources\V1\Customer\Customer;
use App\Http\Resources\V1\CustomerAddress\CustomerAddress;
use App\Http\Resources\V1\Order\OrderResource;
use App\Http\Resources\V1\Order\StoreOrderResource;
use App\Order;

class AffiliateOrderRepository
{
    public function store($request)
    {
        return  Order::create($request);
    }

    public function getOrdersSlotCount($slotDate, $slotStart, $slotEnd)
    {
        return  Order::where(function ($q) use ($slotDate, $slotStart, $slotEnd) {
            $q->where('pickup_date', $slotDate);
            $q->where(function ($q) use ($slotStart, $slotEnd) {
                $q->where('pickup_start', $slotStart);
                $q->where('pickup_end', $slotEnd);
            });
        })
            ->orWhere(function ($q) use ($slotDate, $slotStart, $slotEnd) {
                $q->where('delivery_date', $slotDate);
                $q->where(function ($q)  use ($slotStart, $slotEnd) {
                    $q->where('delivery_start', $slotStart);
                    $q->where('delivery_end', $slotEnd);
                });
            })
            ->count();
    }

    public function show($id)
    {
        $order = Order::Affiliate()->find($id);
        $subTotal = $order->OrderProducts->sum('ProductPrice.price');
        $promoDiscount = utilitiesHelper::promotionCalculate($order->id, $subTotal);
        $vendorpromoDiscount = utilitiesHelper::vendorPromotionCalculate($order->id, $subTotal);
        $totalAmount = utilitiesHelper::totalAmountCalculate($order->id, $subTotal, $promoDiscount + $vendorpromoDiscount);
        $OrderProducts = $order->OrderProducts->groupBy('ProductPrice.service_id');
        $orderPreference = OrderFastPreference::collection($order->OrderFastPreferencies);

        return [
            "order" => new OrderResource($order),
            "details" => ProductDetail::collection($OrderProducts),
            "customer" => new Customer($order["Customer"]),
            "address" => new CustomerAddress($order["CustomerAddress"]),
            "order_preference" => $orderPreference,
            "summary" => [
                "subTotal" => $subTotal,
                "total_amount" => $totalAmount,
            ],
        ];
    }


    public function customerOrders($id)
    {
        $order = Order::Affiliate()->where('customer_id', $id)->get();
        return  $order;
    }


    public function getPromoCount($customerId, $promotionId)
    {
        $orderPromoCount = Order::where('customer_id', $customerId)->where('promotion_id', $promotionId)->count('id');
        return $orderPromoCount;
    }
}
