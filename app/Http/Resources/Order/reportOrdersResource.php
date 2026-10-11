<?php

namespace App\Http\Resources\Order;

use App\Http\Helper\utilitiesHelper;
use App\Http\Resources\ProductPrice\ProductPriceResource;
use App\Order;
use Illuminate\Http\Resources\Json\JsonResource;
use tidy;

class reportOrdersResource extends JsonResource
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
        // $subTotal = $this->OrderProducts->sum('ProductPrice.price');
        $subTotal = utilitiesHelper::orderSubTotal($this->id);

        $promoDiscount = utilitiesHelper::promotionCalculate($this->id, $subTotal);
        $vendorpromoDiscount = utilitiesHelper::vendorPromotionCalculate($this->id, $subTotal);
        $total_amount = utilitiesHelper::totalAmountCalculate($this->id, $subTotal, $promoDiscount + $vendorpromoDiscount);

        return [
            "id" => $this->id,
            "customer_id" => $this->customer_id,
            "location_name" => $this->CustomerAddress["Location"]["city"],
            "item_count" => $this->OrderProducts->count(),
            "Promotion_name" => $this->Promotion["name"],
            "sub_total" => $subTotal,
            "total_amount" => $total_amount,
            "delivery_fee" => $this->delivery_fee,
            "reschedule_fee" => $this->reschedule_fee,
            "discount_amount" => $this->discount_amount,
            "promotion_discount" => $this->promotion_discount,
            "pickup_date" => $this->pickup_date,
            "delivery_date" => $this->delivery_date,
            "created_at" => date($this->created_at),
        ];
    }
}
