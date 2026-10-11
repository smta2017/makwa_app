<?php

namespace App\Http\Resources\V1\Customer;

use App\Http\Resources\V1\Service\ServiceCategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerOrders extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        $vendorServices = [];
        $vvv = $this["Vendor"];
        $vss = $this["Vendor"]->VendorServices;
        foreach ($vss as $value) {
            array_push($vendorServices, $value->Service);
        }


        return [
            'id' => $this["id"],
            'status' => $this["OrderStatus"]["name"],
            'status_id' => $this["status_id"],
            'pickup_date' => $this['pickup_date'],
            'pickup_start_time' => $this['pickup_start'],
            'pickup_end_time' => $this['pickup_end'],
            'delivery_date' => $this['delivery_date'],
            'delivery_start_time' => $this['delivery_start'],
            'delivery_end_time' => $this['delivery_end'],
            'promotion_discount' => $this["promotion_discount"],
            'delivery_fee' => $this["delivery_fee"],
            'tax_amount' => $this["tax_amount"],
            'discount_amount' => $this["discount_amount"],
            'order_cancel' => $this["cancellation_fee"],
            'order_reschedule' => $this["reschedule_fee"],
            'service_fee' => $this["service_fee"],
            'method_of_payment' => $this["method_of_payment"],
            'is_rated' => $this["is_rated"],
            'created_at' => date('Y-m-d H:i:s', strtotime($this["created_at"])),
            'reviews' => $this['reviews'],
            // 'items_count' => $this->OrderProducts->count(),
            "vendor" => [
                "id" => $this["Vendor"]["id"],
                "name" => $this["Vendor"]["name"],
                "icon" => $this["Vendor"]["icon"],
                "service_category" => ServiceCategoryResource::collection($vendorServices),
            ],
        ];
    }
}
