<?php

namespace App\Repositories\Preference;

use App\CustomerPreference;
use App\GeneralCustomerPreference;
use App\GeneralOrderPreference;
use App\GeneralPreference;
use App\OrderFastPreference;
use App\Preference;

class PreferenceRepository
{
    public static function customised_preference($preferencess, $orderId)
    {
        $order_delete = OrderFastPreference::where("order_id", $orderId)->delete();
        $order_delete = GeneralOrderPreference::where("order_id", $orderId)->delete();
        foreach ($preferencess["products"] as $value) {
            $custPref = OrderFastPreference::create(
                ['order_id' => $orderId, 'product_id' => $value['product_id'], 'preference' => ($value['folded']) ? 'folded' : 'hunged']
            );
        };

        foreach ($preferencess["general"] as $value) {
            $custPref = GeneralOrderPreference::create(
                ['order_id' => $orderId, 'general_preference_id' => $value['id'], 'default' => ($value['allow']) ? 1 : 0]
            );
        }
    }

    public static function getAvailable()
    {
        $preferences = [];
        $customer_id = auth()->user()->id;
        $preference = Preference::where('type', 'not_fixed')->get();
        foreach ($preference as $value) {
            $bbv = CustomerPreference::where('customer_id', $customer_id)->where('product_id', $value->product_id)->first();
            if (!is_null($bbv) && $bbv->count() >= 1) {
                array_push($preferences, [
                    "product_id" => $value["product_id"],
                    "product_name" => $value["product"]["name"],
                    "value" => $bbv["preference"],
                ]);
            } else {
                array_push($preferences, [
                    "product_id" => $value["product_id"],
                    "product_name" => $value["product"]["name"],
                    "value" => $value["default_value"],
                ]);
            }
        }
        return $preferences;
    }

    public static function getGeneralPreference()
    {
        $generalPreference = [];
        $customer_id = auth()->user()->id;
        $gP =   GeneralPreference::get();
        foreach ($gP as $value) {
            $bbv = $value->GeneralCustomerPreferences->where('customer_id', $customer_id)->first();
            if (!is_null($bbv) && $bbv->count() >= 1) {
                array_push($generalPreference, [
                    "id" => $value["id"],
                    "title" => $value["title"],
                    "desc" => $value["desc"],
                    "value" => $bbv["default"],
                ]);
            } else {
                array_push($generalPreference, [
                    "id" => $value["id"],
                    "title" => $value["title"],
                    "desc" => $value["desc"],
                    "value" => $value["default"],
                ]);
            }
        }
        return $generalPreference;
    }



    public static function getOrderAvailable($order_id)
    {
        $preferences = [];
        $preference = OrderFastPreference::where('order_id', $order_id)->get();
        foreach ($preference as $value) {
            array_push($preferences, [
                "product_id" => $value["product_id"],
                "product_name" => $value["product"]["name"],
                "value" => $value["preference"],
            ]);
        }
        return $preferences;
    }

    public static function getGeneralOrderPreference($order_id)
    {
        $generalPreference = [];
        $gP =   GeneralOrderPreference::where("order_id", $order_id)->get();
        foreach ($gP as $value) {
            $trt= $value['GeneralPreference'];
            array_push($generalPreference, [
                "id" => $value["general_preference_id"],
                "title" => $value['GeneralPreference']["title"],
                "desc" => $value['GeneralPreference']["desc"],
                "value" => $value["default"],
            ]);
        }
        
        return $generalPreference;
    }
}
