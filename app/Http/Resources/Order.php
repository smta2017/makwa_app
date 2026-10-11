<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Order extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // if ($this->canceled_at=='0') {
            $order_stateus=\App\OrderStatus::find($this->status_id)->name;
            $subTotal=0;
            foreach ($this->OrderProducts as $key => $val) {
                $subTotal += $val->ProductPrice['price'];
            }
            $promoDiscount = 0;
            $total_amount = $this->tax_amount + $this->delivery_fee + $this->service_fee + $this->cancellation_fee + $this->reschedule_fee + $subTotal - $this->discount_amount - $promoDiscount;

            
            
            return[
                "id"=>$this->id,
                "total_price"=>$total_amount,
                "pickup_date"=>$this->pickup_date,
                "pickup_start"=>$this->pickup_start,
                "delivery_date"=>$this->delivery_date,
                "delivery_end"=>$this->delivery_end,
                "created_at"=>\Carbon\Carbon::parse($this->created_at)->format('y-m-d H:i'),  //'7/29/2019 8:58:21 PM',
                "status"=>($order_stateus=='Order placed' || $order_stateus =='Order pickup' || $order_stateus =='Order delivered')? $order_stateus : "Order pickup" ,
                "product_count"=>$this->OrderProducts->count(),
                "is_rated"=>$this->is_rated,
                "method_of_payment"=>$this->method_of_payment,
            ];
            
        // }
    }
}
