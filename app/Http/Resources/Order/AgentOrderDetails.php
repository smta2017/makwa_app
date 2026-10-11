<?php

namespace App\Http\Resources\Order;

use App\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentOrderDetails extends JsonResource
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
        $order_type = "";
        if ($this["pickup_date"] == date('Y-m-d')) {
            $order_type = 'pickup';
        } elseif ($this["delivery_date"] == date('Y-m-d')) {
            $order_type = 'delivery';
        }
        $from_vendor_hanged_confirm = $this->orderHangLables->count();
        if ($from_vendor_hanged_confirm != 0) {
            $from_vendor_hanged_confirm = $this->orderHangLables->whereNull("from_vendor")->count();
            ($from_vendor_hanged_confirm) ? $from_vendor_hanged_confirm = 0 : $from_vendor_hanged_confirm = 1;

            $to_customer_hanged_confirm = $this->orderHangLables->whereNull("to_customer")->count();
            ($to_customer_hanged_confirm) ? $to_customer_hanged_confirm = 0 : $to_customer_hanged_confirm = 1;
        } else {
            $from_vendor_hanged_confirm = null;
            $to_customer_hanged_confirm = null;
        }

        return [
            "id" => $this["id"],
            "order_type" => $order_type,
            "created_at" => $this["created_at"],
            "location" => $this["CustomerAddress"]["Location"]["city"],
            "vendor" => $this["Vendor"]["name"],
            "ordersCount" => count($this["Customer"]["orders"]),
            "status" => $this["OrderStatus"],
            "customer" => [
                "id" => $this["Customer"]["id"],
                "name" => $this["Customer"]["first_name"] . " " . $this["Customer"]["last_name"],
                "phone" => $this["Customer"]["phone"],
                "address" => $this["CustomerAddress"]["street_address"],
            ],
            "pickup" => [
                "date" => $this["pickup_date"],
                "start" => $this["pickup_start"],
                "end" => $this["pickup_end"]
            ], "delivery" => [
                "date" => $this["delivery_date"],
                "start" => $this["delivery_start"],
                "end" => $this["delivery_end"]
            ],
            // "bags" => $bags,
            "services" => AgentOrderService::collection($this->OrderServices),
            "from_vendor_hanged_confirm" => $from_vendor_hanged_confirm,
            "to_customer_hanged_confirm" => $to_customer_hanged_confirm,
            "confirm_route" => $this["confirm_prepare_route"],
        ];
    }
}
