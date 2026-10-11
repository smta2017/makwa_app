<?php

namespace App\Repositories\Vendor;

use App\Vendor;
use App\VendorPromotion;
use Carbon\Carbon;

class VendorRepository
{
  public function getAllVendors()
  {
    $vendors = Vendor::get();
    return $vendors;
  }

  public function getVendorsInLocation($request)
  {
    $query = Vendor::query();

    $query = $query->whereHas('vendorLocations', function ($q) use ($request) {
      $q->where('location_id', $request["location_id"]);
    });

    if ($request["service_category"]) {
      $query->whereHas('VendorServices.Service.ServiceCategory', function ($q) use ($request) {
        $q->where('id', $request["service_category"]);
      });
    }

    if ($request["services_tags"]) {
      $query->whereHas('VendorServiceTags', function ($q) use ($request) {
        $q->whereIn('service_tag_id', $request["services_tags"]);
      });
    }

    if ($request["express"]) {
      $query->whereNotNull('ex_turn_around_iron');
    }

    if ($request["free_delivery"]) {
      $query = $query->where('delivery_fee', 0);
    }
    if ($request["new"]) {
      $beforDate = now()->subDays(env('NEW_VENDOR_DAYS', 20))->format('Y-m-d');
      $query = $query->whereDate('created_at', '>=', $beforDate);
    }
    if ($request["offers"]) {
      $query = $query->where('has_offer', 1);
    }

    if ($request["pickup_slot"]) {
      $slot_time  = Carbon::parse($request["pickup_slot"]);
      $pickup_start = $slot_time->toTimeString();
      $pickup_day = $slot_time->format('l');

      $query->whereHas('VendorSlotsTimes', function ($q) use ($pickup_start, $pickup_day) {
        $q->where('allow', 1);
        $q->whereHas('TimeSlot', function ($q) use ($pickup_start, $pickup_day) {
          $q->where('type', 'pickup');
          $q->active();
          $q->where('day', $pickup_day);
          $q->where('start_time', $pickup_start);
        });
      });
    }

    if ($request["delivery_slot"]) {
      $slot_time  = Carbon::parse($request["delivery_slot"]);
      $delivery_start = $slot_time->toTimeString();
      $delivery_day = $slot_time->format('l');

      $query->whereHas('VendorSlotsTimes', function ($q) use ($delivery_start, $delivery_day) {
        $q->where('allow', 1);
        $q->whereHas('TimeSlot', function ($q) use ($delivery_start, $delivery_day) {
          $q->where('type', 'delivery');
          $q->active();
          $q->where('day', $delivery_day);
          $q->where('start_time', $delivery_start);
        });
      });
    }
    return $query->get();
  }

  public function getVendorDetails($vendor_id)
  {
    $vendor = Vendor::find($vendor_id);
    return $vendor;
  }

  public static function getVendorFilretAttr()
  {
    return  \Config::get('constants.vendor_filter_attributes');
  }

  public static function getVendorRatingAttr()
  {
    $variants = \Config::get('constants.variants_ratings');
    $variants = json_decode(json_encode($variants), true);
    return $variants;
  }

  public static function getVendorPromotions($vendor_id, $theDate)
  {
    $vendor_promotion = VendorPromotion::where('start_date', '<=', ($theDate) ? $theDate : Carbon::now()->toDateString())
      ->where('end_date', '>=', ($theDate) ? $theDate : Carbon::now()->toDateString())
      ->whereNULL('is_archive')
      ->where('vendor_id', $vendor_id)->first();
    return $vendor_promotion;
  }
}
