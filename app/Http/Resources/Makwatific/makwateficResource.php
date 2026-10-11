<?php

namespace App\Http\Resources\Makwatific;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class makwateficResource extends JsonResource
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
            "id" => $this["id"],
            "customer_id" => $this["customer_id"],
            "location_id" => $this["CustomerAddress"]["Location"]["city"],
            "lat" => $this["CustomerAddress"]["latitude"],
            "long" => $this["CustomerAddress"]["longitude"],
            "pickup_date" => $this["pickup_date"],
            "delivery_date" => $this["delivery_date"],
            "order_type" => ($this["pickup_date"] == Carbon::parse($request->fromdate)->format('Y-m-d')) ? "pickup" : "delivery",
            "condetion_date" => Carbon::parse($request->fromdate)->format('Y-m-d'),
        ];
    }
}
