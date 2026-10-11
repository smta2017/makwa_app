<?php

namespace App\Http\Resources\V1\CustomerAddress;

use App\Location;
use Illuminate\Http\Resources\Json\JsonResource;

class AffiliateCustomerAddress extends JsonResource
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
            "id" => $this->id,
            "latitude" => $this->latitude,
            "location_name" => Location::find($this->location_id)["city"],
            "longitude" => $this->longitude,
            "building_no" => $this->building_no,
            "street_address" => $this->street_address,
            "floor_no" => $this->floor_no,
            "apartment_no" => $this->apartment_no,
            "additional_directions" => $this->additional_directions,
            "is_default" => $this->is_default
        ];
    }
}
