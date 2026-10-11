<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorSlotsResource extends JsonResource
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
            "slot_location" => $this->TimeSlot->Location->city,
            "slot_day" => $this->TimeSlot->day,
            "slot_start" => $this->TimeSlot->start_time,
            "slot_end" => $this->TimeSlot->end_time,
            "slot_type" => $this->TimeSlot->type,
            "allow" => $this->allow,
        ];
    }
}
