<?php

namespace App\Http\Resources\V1\Preference;

use App\Http\Resources\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class OtherPreference extends JsonResource
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
            "title" => $this["title"],
            "disc" => $this["desc"],
            "allow" => $this["value"]
        ];
    }
}
