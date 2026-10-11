<?php

namespace App\Http\Resources\Bag;

use Illuminate\Http\Resources\Json\JsonResource;

class scanQRResource extends JsonResource
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
            "opr" =>"pickup",
            "bag_id" => $this->id,
            "bag_service" => $this->service_id,
            "customer" => [
                "id" => $this->customer_bag->Customer->id,
                "name" => $this->customer_bag->Customer->first_name . " " . $this->customer_bag->Customer->last_name,
            ],

        ];
    }
}
