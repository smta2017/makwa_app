<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //  return parent::toArray($request);
        return [
            "order_id" => $this->Order["id"],
            "customer" => [
                "id" => $this->Order["Customer"]["id"],
                "name" => $this->Order["Customer"]["first_name"] . " " . $this->Order["Customer"]["last_name"]
            ],
            "status" => $this->Order["OrderStatus"]["name"],
            "pickup" => [
                "date" => $this->Order["pickup_date"],
                "start" => $this->Order["pickup_start"],
                "end" => $this->Order["pickup_end"]
            ],
            "delivery" => [
                "date" => $this->Order["delivery_date"],
                "start" => $this->Order["delivery_start"],
                "end" => $this->Order["delivery_end"]
            ],
            "vendor"=>$this->vendor_id,
            "order_service_id"=>$this->id,
            "service"=>$this->Service["name"]
        ];
    }
}
