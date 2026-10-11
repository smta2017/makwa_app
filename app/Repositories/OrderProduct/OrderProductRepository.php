<?php

namespace App\Repositories\OrderProduct;

use App\Bag;
use App\Http\Resources\OrderProduct\OrderProductResource;
use App\OrderBag;
use App\OrderProduct;
use App\Service;

class OrderProductRepository
{

    public static function getProductsByOrderId($order_id)
    {
        $OrderProduct = OrderProduct::whereHas('OrderBag', function ($q) use ($order_id) {
            $q->where('order_id', $order_id);
        })->get();
        return $OrderProduct;
    }

    public static function getProductsByBag($order_bag_id)
    {
        $orderBag = OrderProduct::where('order_bag_id', $order_bag_id)->get();
        return $orderBag;
    }

    public static function getOrderProductsByService($order_id, $service_id)
    {
        $orderProducts = OrderProduct::whereHas('OrderBag.Bag', function ($q) use ($service_id) {
            $q->where('service_id', $service_id);
        })->where('order_id', $order_id)->get();
        return $orderProducts;
    }

    public static function getOrderProductsImagesByService($order_id, $service_id)
    {
        $orderProducts = OrderProduct::with('Order')->with('OrderProductImage')->whereHas('OrderBag.Bag', function ($q) use ($service_id) {
            $q->where('service_id', $service_id);
        })->where('order_id', $order_id)->get();
        return $orderProducts;
    }
}
