<?php

namespace App\Http\Helper;

use App\Customer;
use App\GlobalSetting;
use App\Order;
use App\OrderService;
use App\Promotion;
use App\VendorPromotion;
use GuzzleHttp\Client;

class utilitiesHelper
{
    public static function promotionCalculate($orde_id, $subTotal)
    {

        $order = Order::find($orde_id);
        $customer = Customer::find($order->customer_id);

        $promoDiscount = 0;

        $promoUsed = Order::selectRaw('count(id) as count')
            ->where('customer_id', $customer->id)
            ->where('promotion_id', $order->promotion_id)
            ->first();

        $promotion = Promotion::selectRaw('id,type,discount_figure,cap,start_date,end_date,usage_limit,min_order,promo_type')
            ->where('id', $order->promotion_id)
            ->where('start_date', '<=', $order->pickup_date)
            ->where('end_date', '>=', $order->pickup_date)
            ->where('is_archive', 0)
            ->first();

        if ($promotion !== null) {
            if ($promotion->usage_limit == 0 && $subTotal > $promotion->min_order) {
                if ($promotion->type == 'fixed') {
                    $promoDiscount = $promotion->discount_figure;
                } else {
                    $promoDiscount = $subTotal * $promotion->discount_figure / 100;
                    if ($promotion->cap != 0) {
                        if ($promoDiscount > $promotion->cap) {
                            $promoDiscount = $promotion->cap;
                        }
                    }
                }
                // $orderChange=Order::findorfail($orderId);
                $order->promotion_discount = $promoDiscount;
                $order->update();
            } else if (($promotion->usage_limit - $promoUsed->count) > 0 && $subTotal > $promotion->min_order) {
                if ($promotion->type == 'fixed') {
                    $promoDiscount = $promotion->discount_figure;
                } else {
                    $promoDiscount = $subTotal * $promotion->discount_figure / 100;
                    if ($promotion->cap != 0) {
                        if ($promoDiscount > $promotion->cap) {
                            $promoDiscount = $promotion->cap;
                        }
                    }
                }
                // $orderChange=Order::findorfail($orderId);
                $order->promotion_discount = $promoDiscount;
                $order->update();
            }
        }

        return $promoDiscount;
    }

    public static function vendorPromotionCalculate($orde_id, $subTotal)
    {

        $order = Order::find($orde_id);
        $customer = Customer::find($order->customer_id);

        $vendorPromoDiscount = 0;

        $promoUsed = Order::selectRaw('count(id) as count')
            ->where('customer_id', $customer->id)
            ->where('vendor_promotion_id', $order->vendor_promotion_id)
            ->first();

        $promotion = VendorPromotion::selectRaw('id,type,discount_figure,cap,start_date,end_date,usage_limit,min_order')
            ->where('id', $order->promotion_id)
            ->where('start_date', '<=', $order->pickup_date)
            ->where('end_date', '>=', $order->pickup_date)
            ->whereNull('is_archive')
            ->where('vendor_id', $order->vendor_id)
            ->first();

        if ($promotion !== null) {
            if ($promotion->usage_limit == 0 && $subTotal > $promotion->min_order) {
                if ($promotion->type == 'fixed') {
                    $vendorPromoDiscount = $promotion->discount_figure;
                } else {
                    $vendorPromoDiscount = $subTotal * $promotion->discount_figure / 100;
                    if ($promotion->cap != 0) {
                        if ($vendorPromoDiscount > $promotion->cap) {
                            $vendorPromoDiscount = $promotion->cap;
                        }
                    }
                }
                // $orderChange=Order::findorfail($orderId);
                $order->vendor_promotion_discount = $vendorPromoDiscount;
                $order->update();
            } else if (($promotion->usage_limit - $promoUsed->count) > 0 && $subTotal > $promotion->min_order) {
                if ($promotion->type == 'fixed') {
                    $vendorPromoDiscount = $promotion->discount_figure;
                } else {
                    $vendorPromoDiscount = $subTotal * $promotion->discount_figure / 100;
                    if ($promotion->cap != 0) {
                        if ($vendorPromoDiscount > $promotion->cap) {
                            $vendorPromoDiscount = $promotion->cap;
                        }
                    }
                }
                // $orderChange=Order::findorfail($orderId);
                $order->vendor_promotion_discount = $vendorPromoDiscount;
                $order->update();
            }
        }

        return $vendorPromoDiscount;
    }

    public static function orderSubTotal($order_id)
    {
        // $subTotal = 0; 
        // foreach ($order->OrderProducts as $key => $val) {
        //         $subTotal += $val->ProductPrice->price;
        //     }
        $order = Order::find($order_id);
        $subTotal = $order->OrderProducts->sum('ProductPrice.price');

        return $subTotal;
    }

    public static function expressFee($order_id,$subTotal)
    {
        $expressFee = 0;
        $order = Order::find($order_id);
        if ($order->is_express) {
            $expressFee = ($subTotal * $order->Vendor->ex_amount) / 100;
        }
        return $expressFee;

    }

    public static function totalAmountCalculate($orde_id, $subTotal, $promoDiscount)
    {
        $order = Order::find($orde_id);
        $promotion = $order->Promotion;
        $total_amount = 0;
        if ($promotion == null || $promotion->applay_on_delivery == null) {
            $total_amount += $order->tax_amount + $order->nextday_fee + $order->service_fee + $order->cancellation_fee + $order->reschedule_fee + $subTotal - $order->discount_amount - $promoDiscount;
            if ($total_amount < 0) {
                $total_amount = 0;
            }
            $total_amount += $order->delivery_fee;
        } else if ($promotion != null || $promotion->applay_on_delivery != null) {
            $total_amount += $order->tax_amount + $order->nextday_fee + $order->delivery_fee + $order->service_fee + $order->cancellation_fee + $order->reschedule_fee + $subTotal - $order->discount_amount - $promoDiscount;
            if ($total_amount < 0) {
                $total_amount = 0;
            }
        }

        $wash_bag_count = OrderService::where("service_id", 4)->where("order_id", $order->id)->first('bag_count');
        $wash_bag_price = GlobalSetting::select('value')->where('setting_name', 'wash_bag_price')->first();

        if ($wash_bag_count != null) {
            $total_amount +=  ($wash_bag_price->value * $wash_bag_count->bag_count);
        }


        return $total_amount;
    }

    public function avatarOrDefaultAvatar($avatar)
    {
        if (is_null($avatar)) {
            return '/imgs/location.png';
        } else {
            return $avatar;
        }
    }

    public function nullToZeroOrEmpty($val, $NeedTo = '')
    {
        if (is_null($val) || intval($val) == 0) {
            return $NeedTo;
        } else {
            return $val;
        }
    }

    public static function slack_notify($chanel_id, $text)
    {
        // return "oklk";
        $client = new Client();
        $full_url =  "https://hooks.slack.com/services/" . $chanel_id;
        $res = $client->request('POST', $full_url, ['headers' => ['Content-Type' => 'application/json'], 'body' => '{"text":"' . $text . '"}']);
    }


    public static function findInObject2($arr, $thekey, $val)
    {
        $neededObject = 0;
        foreach ($arr as $value) {
            if ($value[$thekey] == $val) {
                $neededObject = $value;
            }
        }
        return $neededObject;
    }

    public static function findInObject($arr, $key, $val)
    {

        $neededObject = [];
        array_filter(
            $arr,
            function ($e) use (&$key, &$val) {

                $tt = [];
                $tt =  $val;
                return  array_values($tt);
            }
        );

        $neededObject = json_encode($neededObject);
        $neededObject = json_decode($neededObject, true);


        return $neededObject;
    }
}
