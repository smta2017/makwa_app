<?php

namespace App\Http\Helper;

use App\Order;
use App\OrderProduct;
use App\Repositories\Order\OrderRepository;
use App\Repositories\OrderProduct\OrderProductRepository;

class preferenceHelper
{

    public static function getProductPreference($product_id)
    {
        $OrderProduct = OrderProduct::find($product_id);
        if ($OrderProduct->ProductPrice->Product->OrderFastPreferencies->where('order_id', $OrderProduct->Order->id)->count() != 0) {
            return substr(ucfirst($OrderProduct->ProductPrice->Product->OrderFastPreferencies->where('order_id', $OrderProduct->Order->id)->first()["preference"]), 0, 1);
        } elseif ($OrderProduct->ProductPrice->Product->CustomerPreferencies->where('customer_id', $OrderProduct->Order->customer_id)->count() != 0) {
            return substr(ucfirst($OrderProduct->ProductPrice->Product->CustomerPreferencies->where('customer_id', $OrderProduct->Order->customer_id)->first()["preference"]), 0, 1);
        } else if ($OrderProduct->ProductPrice->Product->Preference->where('product_id', $OrderProduct->ProductPrice->product_id)->count() != 0) {
            return substr(ucfirst($OrderProduct->ProductPrice->Product->Preference[0]["default_value"]), 0, 1);
        } else {
            return "<span style='color:red'>No Preference!</span>";
        }
    }

    public static function orderPreferenceDetails($order_id)
    {
        $order = Order::find($order_id);
        $OrderProducts = $order->OrderProducts;

        $services = [];
        $total_fold = 0;
        $total_hang = 0;

        $orderServices = OrderRepository::getOrderServices($order_id);

        foreach ($orderServices as $OrderService) {
            $fold = 0;
            $hang = 0;
            $orderProducts = OrderProductRepository::getOrderProductsByService($order_id, $OrderService->id);

            foreach ($orderProducts as $OrderProduct) {
                $prfrence = self::getProductPreference($OrderProduct->id);
                if ($prfrence == "F") {
                    $fold += 1;
                } elseif ($prfrence == "H") {
                    $hang += 1;
                }
            }

            array_push($services, [
                "s_name" => $OrderService->name,
                "service_count" => $orderProducts->count(),
                "Fold" => $fold,
                "Hang" => $hang,
            ],);
            $total_fold += $fold;
            $total_hang += $hang;
        }

        $order_details = [
            "total_items_count" => $OrderProducts->count(),
            "services" => $services,
            "total_fold" => $total_fold,
            "total_hang" => $total_hang,


        ];

        return $order_details;
    }
}
