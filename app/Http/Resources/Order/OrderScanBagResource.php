<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderScanBagResource extends JsonResource
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
            "success" => 1,
            "opr" => "pickup",
            "bag" => [
                "id" => $this["bag"]["id"],
                "service" => $this["bag"]["service_id"],
                "status" => $this["bag"]["status_id"]
            ],
            "order" => [
                "id" => $this["order"]["id"],
                "created_at" => $this["order"]["created_at"],
                "customer" => [
                    "id" => $this["order"]["Customer"]["id"],
                    "name" => $this["order"]["Customer"]["first_name"] . " " . $this["order"]["Customer"]["last_name"]
                ],
                "status" => $this["order"]["OrderStatus"]["name"],
                "pickup" => [
                    "date" => $this["order"]["pickup_date"],
                    "start" => $this["order"]["pickup_start"],
                    "end" => $this["order"]["pickup_end"]
                ],
                "delivery" => [
                    "date" => $this["order"]["delivery_date"],
                    "start" => $this["order"]["delivery_start"],
                    "end" => $this["order"]["delivery_end"]
                ],
                "vendor" => $this["order"]["Vendor"]["name"],
                "ordersCount"=> count($this["order"]["Customer"]["orders"])
            ],
        ];
    }
}
