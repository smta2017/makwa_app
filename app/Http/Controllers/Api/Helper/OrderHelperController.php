<?php

namespace App\Http\Controllers\Api\Helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CustomerAddress;
use App\Vendor;
use App\vendorLocation;
use App\VendorSlotsTime;
use Carbon\Carbon;
use DB;

use function GuzzleHttp\Promise\queue;

class OrderHelperController extends Controller
{
    public static function orderRoutingVendor($order)
    {
        $address = CustomerAddress::find($order["address_id"]);

        $vendors_in_location = vendorLocation::where('location_id', $address["location_id"])->where("available", 1)->get();

        $pickup_day = Carbon::parse($order["pickup_date"])->isoFormat('dddd');
        $order_pickup_start =  $order["pickup_start"];
        $order_pickup_end =  $order["pickup_end"];

        $delivery_day = Carbon::parse($order["delivery_date"])->isoFormat('dddd');
        $order_delivery_start =  $order["delivery_start"];
        $order_delivery_end =  $order["delivery_end"];
        $service_tags = $order["service_tag_id"];
        $order_slots = [
            "pickup_day" => $pickup_day,
            "pickup_start" => $order_pickup_start,
            "pickup_end" => $order_pickup_end,

            "delivery_day" => $delivery_day,
            "delivery_start" => $order_delivery_start,
            "delivery_end" => $order_delivery_end,
        ];



        $vendor = Vendor::query();
        $vendor = $vendor->where(function ($q) use ($order_slots) {
            $q->whereHas('VendorSlotsTimes', function ($q) use ($order_slots) {
                $q->where('allow', 1);
                $q->whereHas('TimeSlot', function ($q) use ($order_slots) {
                    $q->where('type', 'pickup');
                    $q->where('day', $order_slots["pickup_day"]);
                    $q->where('start_time', $order_slots["pickup_start"]);
                    $q->where('end_time', $order_slots["pickup_end"]);
                });
            });
            $q->whereHas('VendorSlotsTimes', function ($q) use ($order_slots) {
                $q->where('allow', 1);
                $q->whereHas('TimeSlot', function ($q) use ($order_slots) {
                    $q->where('type', 'delivery');
                    $q->where('day', $order_slots["delivery_day"]);
                    $q->where('start_time', $order_slots["delivery_start"]);
                    $q->where('end_time', $order_slots["delivery_end"]);
                });
            });
        })->whereHas("vendorLocations", function ($q) use ($address) {
            $q->where('location_id', $address["location_id"]);
            $q->where("available", 1);
        });
        if (isset($service_tags) && $service_tags != null) {
            foreach ($service_tags as $value) {
                $vendor = $vendor->whereHas('VendorServiceTags', function ($q) use ($value) {
                    $q->where('service_tag_id', $value);
                });
            }
        }
        $vendor = $vendor->first();

        // return $vendor;
        //=======================

        if ($vendor != null) {
            return $vendor->id;
        }

        $default_vendor =  $vendors_in_location->where('head', 1)->first();
        if ($default_vendor != null) {
            return $default_vendor->vendor_id;
        }
    }

    public static function getActiveVndorByLocation($location_id)
    {
        $vendors = Vendor::where("location_id", $location_id)->where("active", 1)->get();
        return $vendors;
    }
}
