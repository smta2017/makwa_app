<?php

namespace App\Http\Resources\Order;

use App\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentOrdersResource extends JsonResource
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
        $order = Order::find($this->id);
        $OrderServices = $order->OrderServices;
        $bags = array();
        foreach ($OrderServices as $OrderService) {
            foreach ($OrderService->OrderBags as $OrderBag) {
                $bag = [
                    "id" => $OrderBag->bag->id,
                    "qrcode" => $OrderBag->bag->qrcode,
                    "service" => $OrderBag->bag->Service,
                ];
                array_push($bags, $bag);
            }
        }
        $order_type = "";
        if ($this["pickup_date"] == date('Y-m-d')) {
            $order_type = 'pickup';
        } elseif ($this["delivery_date"] == date('Y-m-d')) {
            $order_type = 'delivery';
        }
        return [
            "id" => $this["id"],
            "order_type" => $order_type,
            "created_at" => $this["created_at"],
            "customer" => [
                "id" => $this["Customer"]["id"],
                "name" => $this["Customer"]["first_name"] . " " . $this["Customer"]["last_name"]
            ],
            "status" => $this["OrderStatus"],
            "pickup" => [
                "date" => $this["pickup_date"],
                "start" => $this["pickup_start"],
                "end" => $this["pickup_end"]
            ],
            "bags" => $bags,
            "services" => $this->OrderServices,
            "delivery" => [
                "date" => $this["delivery_date"],
                "start" => $this["delivery_start"],
                "end" => $this["delivery_end"]
            ],
            "vendor" => $this["Vendor"]["name"],
            "confirmed_route" => $this["confirm_prepare_route"],
            "ordersCount" => count($this["Customer"]["orders"])
        ];
    }
}
