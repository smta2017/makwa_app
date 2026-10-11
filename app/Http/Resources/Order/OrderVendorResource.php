<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderVendorResource extends JsonResource
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
        // $order = $this->customer_bags[0]->Customer->Orders->where("status_id", 1)->where('pickup_date', date('Y-m-d'))->first();
        return [
            "id" => $this["id"],
            "created_at" => $this["created_at"],
            "customer" => [
                "id" => $this["Customer"]["id"],
                "name" => $this["Customer"]["first_name"] . " " . $this["Customer"]["last_name"]
            ],
            "status" => $this["OrderStatus"]["name"],
            "pickup" => [
                "date" => $this["pickup_date"],
                "start" => $this["pickup_start"],
                "end" => $this["pickup_end"]
            ],
            "delivery" => [
                "date" => $this["delivery_date"],
                "start" => $this["delivery_start"],
                "end" => $this["delivery_end"]
            ],
            "vendor" => $this["Vendor"]["name"],
            "ordersCount" => count($this["Customer"]["orders"])
        ];
    }
}
