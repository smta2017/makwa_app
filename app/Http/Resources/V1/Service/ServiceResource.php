<?php

namespace App\Http\Resources\V1\Service;

use App\Http\Helper\utilitiesHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $helper = new  utilitiesHelper();
        // return parent::toArray($request);
        return [
            'id' => $this["id"],
            'name' => $this["name"],
        ];
    }
}
