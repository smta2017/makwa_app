<?php

namespace App\Http\Resources\OrderProduct;

use App\Http\Resources\Product\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
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
            "price" =>  $this['ProductPrice']['price'],
            "order_id" => $this->order_id,
            "item_color" => $this->item_color,
            "service_id" => $this->OrderBag->Bag->Service->id,
            "service_name" => $this->OrderBag->Bag->Service->name,
        ];
    }
}
