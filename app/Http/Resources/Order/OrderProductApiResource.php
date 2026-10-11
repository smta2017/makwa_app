<?php

namespace App\Http\Resources\Order;

use App\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductApiResource extends JsonResource
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
            "prfrence" => \App\Http\Helper\preferenceHelper::getProductPreference($this->id),
            "p_count" => $this->p_count,
            "pp_id" =>  $this->product_price_id,
            "p_id" =>  $this->id,
            "name" => $this['ProductPrice']['Product']['name'],
            "name_ar" =>  $this['ProductPrice']['Product']['name_ar'],
            "order_id" => $this->order_id,
            "item_color" => $this->item_color,
        ];
    }
}
