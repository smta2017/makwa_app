<?php

namespace App\Http\Resources\V1\Order;

use App\Http\Resources\V1\CustomerAddress\CustomerAddress;
use App\Http\Resources\V1\Service\ServiceCategoryResource;
use App\Http\Resources\V1\Vendor\VendorResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreOrderResource extends JsonResource
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
        $vss=$this['vendor']->VendorServices;
        foreach ($vss as $value) {
            array_push($vendorServices, $value->Service);
        }

        
        return [
            'id' => $this["id"],
            'customer_id' => $this["customer_id"],
            'pickup_date' => $this['pickup_date'],
            'pickup_start_time' => $this['pickup_start'],
            'pickup_end_time' => $this['pickup_end'],
            'delivery_date' => $this['delivery_date'],
            'delivery_start_time' => $this['delivery_start'],
            'delivery_end_time' => $this['delivery_end'],
            "vendor" => [
                "id" => $this['vendor']["id"],
                "name" => $this['vendor']["name"],
                "icon" => $this['vendor']["icon"],
                "service_category" => ServiceCategoryResource::collection($vendorServices),
                "vendor_service_tags" => $this['vendor']["VendorServiceTags"],
            ],
            'address' => new CustomerAddress($this->CustomerAddress),
        ];
    }
}
