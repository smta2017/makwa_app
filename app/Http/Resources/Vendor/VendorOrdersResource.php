<?php

namespace App\Http\Resources\Vendor;

use App\Http\Resources\V1\bag\bag ;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorOrdersResource extends JsonResource
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
        return [
            "id" => $this["id"],
            "customer" => [
                "id" => $this["Customer"]["id"],
                "name" => $this["Customer"]["first_name"] . " " . $this["Customer"]["last_name"]
            ],
            "location" => $this["CustomerAddress"]["Location"]["city"],
            "status" => $this["OrderStatus"]["name"],
            "status_id" => $this["OrderStatus"]["id"],
            "pickup" => [
                "full_date" => date('Y-m-d', strtotime($this["pickup_date"])),
                "day" => date('D', strtotime($this["pickup_date"])),
                "day_date" => date('d', strtotime($this["pickup_date"])),
                "mon_yer" => date('Y-m', strtotime($this["pickup_date"])),
                "start" => date('H:i A', strtotime($this["pickup_start"])),
                "end" => date('h:i A', strtotime($this["pickup_end"]))
            ],
            "delivery" => [
                "full_date" => date('Y-m-d', strtotime($this["delivery_date"])),
                "day" => date('D', strtotime($this["delivery_date"])),
                "day_date" => date('d', strtotime($this["delivery_date"])),
                "mon_yer" => date('Y-m', strtotime($this["delivery_date"])),
                "start" => date('h:i A', strtotime($this["delivery_start"])),
                "end" => date('h:i A', strtotime($this["delivery_end"]))
            ],
            "nextday_fee" => $this["nextday_fee"],
            "services" => $this["OrderServices"],
            "method_of_payment" => $this["method_of_payment"],
            "created_at" => date('Y-m-d h:i', strtotime($this["created_at"])),
            "order_bags" => bag::collection($this->OrderBags)
        ];
    }
}
