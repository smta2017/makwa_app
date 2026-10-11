<?php

namespace App\Http\Resources\V1\ServiceTag;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceTagResource extends JsonResource
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
            "id" => $this['ServiceTag']['id'],
            "name" => $this["ServiceTag"]["name"],
        ];
    }
}
