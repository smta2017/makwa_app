<?php

namespace App\Http\Resources\V1\Order;

use App\Http\Resources\V1\Promotion\Promotion;
use App\OrderProduct;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'id' => $this["id"],
            'status' => ($this["OrderStatus"]['name'] == 'Cleaning' || $this["OrderStatus"]['name'] == 'Pending Delivery' || $this["OrderStatus"]['name'] == 'Pending Vendor') ? "Order pickup" : $this["OrderStatus"]['name'],
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
            'selected_card' => $this["selected_card"],
            'customer_notes' => $this["customer_notes"],
            // 'wallet_snap' => intval($this["wallet_val"]),
            'promotion' => new Promotion($this["Promotion"]),
            'is_rated' => $this["is_rated"],
            'created_at' => date('Y-m-d H:i:s', strtotime($this["created_at"])),
        ];
    }
}
