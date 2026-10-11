<?php

namespace App\Http\Resources\V1\CustomerAddress;

use App\Location;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAddress extends JsonResource
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
            "longitude" => $this->longitude,
            "location_id" => $this->location_id,
            "location_name" => Location::find($this->location_id)["city"],
            "building_no" => $this->building_no,
            "street_address" => $this->street_address,
            "floor_no" => $this->floor_no,
            "apartment_no" => $this->apartment_no,
            "type" => $this->type,
            "additional_directions" => $this->additional_directions,
            "is_default" => $this->is_default,
            "country_code" => $this->country_code,
            "phone_number" => $this->phone_number,
            "landline" => $this->landline,
            "building_type" => $this->building_type,
        ];
    }
}
