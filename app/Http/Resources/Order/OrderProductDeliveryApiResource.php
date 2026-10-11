<?php

namespace App\Http\Resources\Order;

use App\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductDeliveryApiResource extends JsonResource
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
        $fold = array();
        $hang =array();
        
        if (\App\Http\Helper\preferenceHelper::getProductPreference($this->op_id) == "F") {
            array_push(
                $fold,
                [
                    "prfrence" => \App\Http\Helper\preferenceHelper::getProductPreference($this->op_id),
                    "p_count" => $this->p_count,
                    "pp_id" =>  $this->pp_id,
                    "name" => $this->name,
                    "order_id" => $this->order_id
                ]
            );
        } elseif (\App\Http\Helper\preferenceHelper::getProductPreference($this->op_id) == "H") {
            array_push(
                $hang,
                [
                    "prfrence" => \App\Http\Helper\preferenceHelper::getProductPreference($this->op_id),
                    "p_count" => $this->p_count,
                    "pp_id" =>  $this->pp_id,
                    "name" => $this->name,
                    "order_id" => $this->order_id
                ]
            );
        }

        return [

            "fold" => $fold,
            "hang" => $hang,

        ];
    }
}
