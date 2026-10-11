<?php

namespace App\Http\Resources\Order;

use App\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductApiInvoiceResource extends JsonResource
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
            "prfrence" => \App\Http\Helper\preferenceHelper::getProductPreference($this->op_id),
            "p_count" => $this->p_count,
            "pp_id" =>  $this->pp_id,
            "name" => $this['name'],
            "name_ar" => $this->name_ar,
            "order_id" => $this->order_id,
            "item_color" => $this->item_color,
        ];
    }
}
